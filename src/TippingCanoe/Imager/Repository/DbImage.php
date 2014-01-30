<?php namespace TippingCanoe\Imager\Repository;

use TippingCanoe\Imager\Model\Image as ImageModel;
use TippingCanoe\Imager\Model\Imageable;


class DbImage implements Image {

	/**
	 * Creates a new Image object in the database.
	 *
	 * @param $attributes
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function create($attributes) {
		return ImageModel::create($attributes);
	}

	/**
	 * Gets an image object by it's id.
	 *
	 * @param int $id
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function getById($id) {
		return ImageModel::find($id);
	}

	/**
	 * @param $slot
	 * @param Imageable $imageable
	 * @return \TippingCanoe\Imager\Model\Image
	 */
	public function getBySlot($slot, Imageable $imageable = null) {

		if($imageable)
			$query = $imageable->images();
		else
			$query = ImageModel::unattached();

		return $query->inSlot($slot)->first();

	}

}