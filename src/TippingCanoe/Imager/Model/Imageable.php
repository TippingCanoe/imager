<?php namespace TippingCanoe\Imager\Model;


interface Imageable {

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function images();

}
