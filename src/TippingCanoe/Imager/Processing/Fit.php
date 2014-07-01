<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\Image as Intervention;

/**
 * Class Fit
 *
 * Combines cropping and resizing of an image.
 *
 * @package TippingCanoe\Imager\Processing
 */
class Fit implements Filter {

	/** @var int */
	protected $width;

	/** @var int */
	protected $height;

	/**
	 * @param int $width
	 */
	public function setWidth($width) {
		$this->width = $width;
	}

	/**
	 * @param int $height
	 */
	public function setHeight($height) {
		$this->height = $height;
	}

	public function process(File $file, Image $image, array $config = null) {

		$imageData = new Intervention($file->getRealPath());

		$imageData->fit($this->width, $this->height);

		$imageData->save(null, 100);

	}

}
