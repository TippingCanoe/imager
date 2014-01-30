<?php namespace TippingCanoe\Imager\Repository;

use TippingCanoe\Imager\Model\Imageable;


interface Image {

	/**
	 * Creates a new Image object in the database.
	 *
	 * @param $attributes
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function create($attributes);

	/**
	 * Gets an image object by it's id.
	 *
	 * @param int $id
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function getById($id);

	/**
	 * @param $slot
	 * @param Imageable $imageable
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function getBySlot($slot, Imageable $imageable = null);

}
