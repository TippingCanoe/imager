<?php namespace TippingCanoe\Imager;

use Illuminate\Support\Facades\Facade as Base;


class Facade extends Base {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'TippingCanoe\Imager\Service';
	}

}