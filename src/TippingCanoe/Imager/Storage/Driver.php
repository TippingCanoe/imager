<?php namespace TippingCanoe\Imager\Storage;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Interface Driver
 *
 * Represents a class that can be used as a storage driver for images.
 *
 * @package TippingCanoe\Imager\Storage
 */
interface Driver {

	/**
	 * Saves an image.
	 *
	 * Exceptions can provide extended error information and will abort the save process.
	 *
	 * @param File $file
	 * @param Image $image
	 * @param array $filters
	 */
	public function saveFile(File $file, Image $image, array $filters = []);

	/**
	 * Returns the public URI for an image by a specific configuration.
	 *
	 * @param Image $image
	 * @param array $filters
	 * @return string
	 */
	public function getPublicUri(Image $image, array $filters = []);

	/**
	 * Asks the driver if it has a particular image.
	 *
	 * @param \TippingCanoe\Imager\Model\Image $image
	 * @param array $filters
	 * @return boolean
	 */
	public function has(Image $image, array $filters = []);

	/**
	 * Tells the driver to delete an image.
	 *
	 * Deleting must at least ensure that afterwards, any call to has() returns false.
	 *
	 * @param Image $image
	 * @param array $filters
	 */
	public function delete(Image $image, array $filters = []);

	/**
	 * Tells the driver to prepare a copy of the original image locally.
	 *
	 * @param Image $image
	 * @return File
	 */
	public function tempOriginal(Image $image);

}