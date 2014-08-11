<?php namespace TippingCanoe\Imager\Storage;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
/**
 * Abstract BaseStorage class
 *
 * @package TippingCanoe\Imager\Storage
 */
abstract class BaseStorage implements Driver {

	abstract function saveFile(File $file, Image $image, array $filters = []);
	abstract function getPublicUri(Image $image, array $filters = []);
	abstract function delete(Image $image, array $filters = []);
	abstract function tempOriginal(Image $image);

	/**
	 * Generates a hash based on an image and it's filters.
	 *
	 * @param Image $image
	 * @param array $filters
	 * @return string
	 */
	protected function generateHash(Image $image, array $filters = []) {

		$state = [
			'id' => (string)$image->getKey(),
			'filters' => $filters
		];

		// Must be recursively sorted otherwise arrays with similar keys in different orders won't have the same hash!
		$state = $this->recursiveKeySort($state);

		return md5(json_encode($state));

	}
} 