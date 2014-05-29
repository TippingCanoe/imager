<?php namespace TippingCanoe\Imager;

use Exception;
use Illuminate\Foundation\Application;
use TippingCanoe\Imager\Model\Image;
use Intervention\Image\Image as Intervention;
use TippingCanoe\Imager\Processing\Filter;
use TippingCanoe\Imager\Repository\Image as ImageRepository;
use Symfony\Component\HttpFoundation\File\File;
use TippingCanoe\Imager\Model\Imageable;


class Service {

	/** @var \TippingCanoe\Imager\Repository\Image */
	protected $imageRepository;

	/** @var \Intervention\Image\Facades\Image */
	protected $intervention;

	/** @var \TippingCanoe\Imager\Storage\Driver[] */
	protected $storageDrivers;

	/** @var \TippingCanoe\Imager\Storage\Driver */
	protected $currentDriver;

	/** @var \Illuminate\Foundation\Application */
	protected $app;

	/**
	 * @param ImageRepository $imageRepository
	 * @param Intervention $intervention
	 * @param Application $app
	 * @param \TippingCanoe\Imager\Storage\Driver[] $storageDrivers
	 * @throws Exception
	 */
	public function __construct(
		ImageRepository $imageRepository,
		Intervention $intervention,
		Application $app,
		array $storageDrivers
	) {

		$this->imageRepository = $imageRepository;
		$this->intervention = $intervention;
		$this->app = $app;

		if(empty($storageDrivers))
			throw new Exception('You must configure at least one image storage driver for Imager.');

		$this->storageDrivers = $storageDrivers;
		$this->currentDriver = current($storageDrivers);

	}

	//
	// General Methods
	//

	/**
	 * Select which driver Imager uses by default.
	 *
	 * @param $abstract
	 */
	public function useDriver($abstract) {
		$this->currentDriver = $this->storageDrivers[$abstract];
	}

	/**
	 * Simply retrieves an image by id.
	 *
	 * @param int $id
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function getById($id) {
		return $this->imageRepository->getById($id);
	}

	/**
	 * @param $slot
	 * @param Imageable $imageable
	 * @return Image
	 */
	public function getBySlot($slot, Imageable $imageable = null) {
		return $this->imageRepository->getBySlot($slot, $imageable);
	}

	/**
	 * @param \TippingCanoe\Imager\Model\Image $image
	 * @param array $filters
	 * @return string
	 */
	public function getPublicUri(Image $image, array $filters = []) {

		if(!$this->getDriver()->has($image, $filters)) {
			$tempOriginal = $this->getDriver()->tempOriginal($image);
			$this->saveFile($tempOriginal, $image, $filters);
		}

		return $this->getDriver()->getPublicUri($image, $filters);

	}

	/**
	 * Returns an image URI based on the id of the original.
	 *
	 * @param int $id
	 * @param array $filters
	 * @return string
	 */
	public function getPublicUriById($id, array $filters = []) {
		return $this->getPublicUri($this->getById($id), $filters);
	}

	/**
	 * Returns an image URI based on the slot and imageable.
	 *
	 * @param string $slot
	 * @param Imageable $imageable
	 * @param array $filters
	 * @return string
	 */
	public function getPublicUriBySlot($slot, Imageable $imageable = null, $filters = []) {
		return $this->getPublicUri($this->getBySlot($slot, $imageable), $filters);
	}

	/**
	 * Saves a new image from a file found on the server's filesystem.
	 *
	 * @param File $file
	 * @param \TippingCanoe\Imager\Model\Imageable|\Illuminate\Database\Eloquent\Model $imageable
	 * @param array $attributes
	 * @throws Exception
	 * @return null
	 */
	public function saveFromFile(File $file, Imageable $imageable = null, array $attributes = []) {

		if(!array_key_exists($file->getMimeType(), Mime::getTypes()))
			throw new Exception(sprintf('File type %s not supported', $file->getMimeType()));

		$image = $this->createImageRecord($file, $attributes);

		// Believe it or not, imageables are optional!
		if($imageable)
			$imageable->images()->save($image);

		$this->saveFile($file, $image);

		return $image;

	}

	/**
	 * Saves a new image from a file available via any of the standard PHP supported schemes.
	 *
	 * @param string $uri
	 * @param \TippingCanoe\Imager\Model\Imageable $imageable
	 * @param array $attributes
	 * @return null
	 */
	public function saveFromUri($uri, Imageable $imageable = null, array $attributes = []) {

		// Download the file.
		// Use sys_get_temp_dir so that systems-level configs can apply.
		$tempFilePath = tempnam(sys_get_temp_dir(), null);
		file_put_contents($tempFilePath, fopen($uri, 'r'));

		$tempFile = new File($tempFilePath);

		return $this->saveFromFile($tempFile, $imageable, $attributes);

	}

	/**
	 * @param Image $image
	 * @param array $filters
	 */
	public function delete(Image $image, array $filters = []) {

		$this->getDriver()->delete($image, $filters);

		// If we're deleting the original, also remove the database entry.
		if(!$filters)
			$image->delete();

	}

	/**
	 * @param $id
	 * @param array $filters
	 */
	public function deleteById($id, array $filters = []) {
		$this->delete($this->getById($id), $filters);
	}

	public function deleteBySlot($slot, Imageable $imageable = null) {
		$this->delete($this->getBySlot($slot, $imageable));
	}

	//
	// Slot Methods
	//

	/**
	 * @param array $operations
	 * @param \Symfony\Component\HttpFoundation\File\File[] $files
	 * @param Imageable $imageable
	 */
	public function batch(array $operations, array $files = null, Imageable $imageable = null) {

		// Perform any operations first so that images can move out of the way for new ones.
		foreach($operations as $slot => $operation) {

			// Do deletes first.
			if(empty($operation))
				$this->deleteBySlot($slot, $imageable);

			// Then move/swaps.
			elseif(is_int($operation))
				$this->moveToSlot($this->getById($operation), $slot);

			// Then remote images.
			elseif(filter_input($operation, FILTER_VALIDATE_URL)) {

				try {
					$this->saveFromUri($operation, $imageable, ['slot' => $slot]);
				}
				catch(Exception $ex) {
					// Displace whatever is in the slot.
					$this->moveToSlot($this->getBySlot($slot), null);
					$this->saveFromUri($operation, $imageable, ['slot' => $slot]);
				}

			}

		}

		// Handle file uploads.
		foreach($files as $file) {
			try {
				$this->saveFromFile($file, $imageable, ['slot' => $slot]);
			}
			catch(Exception $ex) {
				// Displace whatever is in the slot.
				$this->moveToSlot($this->getBySlot($slot), null);
				$this->saveFromFile($file, $imageable, ['slot' => $slot]);
			}
		}

	}

	public function moveToSlot(Image $image, $slot) {

		try {
			// Assign the new slot to our image.
			$image->slot = $slot;
			$image->save();
		}
		// Something is already in our slot.
		catch(Exception $ex) {

			// Move the previous image out temporarily, we'll perform a swap.
			$previousSlotImage = $this->getBySlot($slot, $image->imageble);
			$previousSlotImage->slot = null;
			$previousSlotImage->save();

			// Save the slot our image is in.
			$previousSlot = $image->slot;
			// NOW save!
			$image->slot = $slot;
			$image->save();

			// If our image had a non-null slot, move the previous occupant of the target slot into it.
			if($previousSlot !== null) {
				$previousSlotImage->slot = $previousSlot;
				$previousSlotImage->save;
			}

		}

	}

	//
	// Utility Methods
	//

	/**
	 * Gets the current or specified driver.
	 *
	 * @param null $abstract
	 * @return \TippingCanoe\Imager\Storage\Driver
	 */
	protected function getDriver($abstract = null) {
		return $abstract ? $this->storageDrivers[$abstract] : $this->currentDriver;
	}

	/**
	 * Create the database entry for an image.
	 *
	 * @param File $image
	 * @param array $attributes
	 * @return Image
	 */
	protected function createImageRecord(File $image, array $attributes = []) {

		// Obtain image metadata and save the record to the database.
		$imageData = new ImageData($image);
		$attributes = array_merge($attributes, [
			'width' => $imageData->getWidth(),
			'height' => $imageData->getHeight(),
			'average_color' => $imageData->getAveragePixelColor(),
			'mime_type' => $image->getMimeType()
		]);
		return $this->imageRepository->create($attributes);

	}

	/**
	 * Pass a file save into the current Driver.
	 *
	 * @param File $file
	 * @param Model\Image $image
	 * @param array $filters
	 * @throws \Exception
	 */
	protected function saveFile(File $file, Image $image, array $filters = []) {
		$this->runFilters($file, $image, $filters);
		$this->getDriver()->saveFile($file, $image, $filters);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\File\File $file
	 * @param \TippingCanoe\Imager\Model\Image $image
	 * @param array $filters
	 * @throws \Exception
	 */
	protected function runFilters(File $file, Image $image, array $filters = []) {

		foreach($filters as $key => $filter) {

			/** @var \TippingCanoe\Imager\Processing\Filter $abstract */
			if(is_array($filter) && !empty($filter)) {

				$abstract = $this->app->make($filter[0]);

				// If there are config params.
				if(!empty($filter[1])) {
					foreach($filter[1] as $property => $value) {
						$setter = studly_case('set_' . $property);
						$abstract->$setter($value);
					}
				}

			}
			elseif(is_string($filter))
				$abstract = $this->app->make($filter);
			else
				throw new Exception(sprintf('Filter #%s is misconfigured.', $key));

			if(!$abstract)
				throw new Exception(sprintf('Unable to resolve filter \'%s\'.', $abstract));

			if(!$abstract instanceof Filter)
				throw new Exception(sprintf('Class %s does not implement Filter.', $filter[0]));

			// Each filter manipulates the temporary file in-place. They must not move or delete it.
			$abstract->process($file, $image);

		}

	}

}