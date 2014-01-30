<?php namespace TippingCanoe\Imager\Model;


trait ImageableImpl {

	public function images() {
		return $this->morphMany('TippingCanoe\Imager\Model\Image', 'imageable');
	}

}