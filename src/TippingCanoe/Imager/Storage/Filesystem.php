<?php namespace TippingCanoe\Imager\Storage;

use TippingCanoe\Imager\Model\Image;
use TippingCanoe\Imager\Mime;
use Symfony\Component\HttpFoundation\File\File;

class Filesystem extends Base {

	/** @var string */
	protected $publicPrefix;

	/** @var string */
	protected $root;

	/**
	 * @param $path
	 */
	public function setRoot($path) {
		$this->root = $path;
	}

	/**
	 * @param string $prefix
	 */
	public function setPublicPrefix($prefix) {
		$this->publicPrefix = $prefix;
	}

	//
	// Public Interface Implementation
	//

	/**
	 * @param \TippingCanoe\Imager\Model\Image $image
	 * @param array $filters
	 * @return string
	 */
	public function getPublicUri(Image $image, array $filters = []) {
		return sprintf('%s/%s',
			$this->getPublicPrefix(),
			$this->generateFileName($image, $filters)
		);
	}

	/**
	 * Saves an image.
	 *
	 * Exceptions can provide extended error information and will abort the save process.
	 *
	 * @param File $file
	 * @param Image $image
	 * @param array $filters
	 */
	public function saveFile(File $file, Image $image, array $filters = []) {
		$file->move($this->root, $this->generateFileName($image, $filters));
	}

	/**
	 * @param Image $image
	 * @param array $filters
	 * @return bool|mixed
	 */
	public function has(Image $image, array $filters = []) {
		return file_exists($this->generateFilePath($image, $filters));
	}

	/**
	 * Deletes an image.
	 *
	 * If the image is the original, also removes all derived images.
	 *
	 * @param Image $image
	 * @param array $filters
	 */
	public function delete(Image $image, array $filters = []) {

		// If we're deleting a derived image.
		if($filters) {
			unlink($this->generateFilePath($image, $filters));
		}
		// If we're deleting an original, catch all it's derivatives as well.
		else {

			$pattern = sprintf('%s/%s-*.%s',
				$this->root,
				$image->getKey(),
				Mime::getExtensionForMimeType($image->mime_type)
			);

			foreach(glob($pattern) as $filePath) {
				unlink($filePath);
			}

		}

	}

	/**
	 * Tells the driver to prepare a copy of the original image locally.
	 *
	 * @param Image $image
	 * @return File
	 * @throws \Exception
	 */
	public function tempOriginal(Image $image) {

		$originalPath = sprintf('%s/%s-%s.%s',
			$this->root,
			$image->getKey(),
			$this->generateHash($image),
			Mime::getExtensionForMimeType($image->mime_type)
		);

		$tempOriginalPath = tempnam(sys_get_temp_dir(), null);

		if (!copy($originalPath, $tempOriginalPath)) {
			throw new \Exception('Imager couldn\'t copy the original image to the temporary location');
		}

		return new File($tempOriginalPath);

	}

	//
	// Utility Methods
	//

	/**
	 * @return string
	 */
	protected function getPublicPrefix() {
		return $this->publicPrefix;
	}

	/**
	 * @param Image $image
	 * @param array $filters
	 * @return string
	 */
	protected function generateFilePath(Image $image, array $filters = []) {
		return sprintf('%s/%s', $this->root, $this->generateFileName($image, $filters));
	}

}