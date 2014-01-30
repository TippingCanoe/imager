<?php namespace TippingCanoe\Imager;

class Mime {

	/**
	 * We uniquely identify & index image types by their mime type, these are the ones supported.
	 *
	 * @var array
	 */
	protected static $types = [

		'image/gif' => [
			'extension' => 'gif',
			'imagetype' => IMAGETYPE_GIF
		],
		'image/jpeg' => [
			'extension' => 'jpg',
			'imagetype' => IMAGETYPE_JPEG
		],
		'image/png' => [
			'extension' => 'png',
			'imagetype' => IMAGETYPE_PNG
		],
		//'image/bmp' => [
		//	'extension' => 'bmp',
		//	'imagetype' => IMAGETYPE_BMP
		//],

	];

	/**
	 * Returns the correct extension for a mime type.
	 *
	 * @param string $mimeType
	 * @return string
	 */
	public static function getExtensionForMimeType($mimeType) {
		return static::$types[$mimeType]['extension'];
	}

	public static function getTypes() {
		return static::$types;
	}

}