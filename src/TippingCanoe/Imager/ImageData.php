<?php namespace TippingCanoe\Imager;

use Intervention\Image\Image as Intervention;
use Symfony\Component\HttpFoundation\File\File;


/**
 * Class ImageData
 *
 * Represents some internal information we'd like to gather on image originals prior to storage.
 *
 * @package TippingCanoe\Imager
 */
class ImageData {

	/** @var \Symfony\Component\HttpFoundation\File */
	protected $file;

	/** @var \Intervention\Image\Image */
	protected $intervention;

	/**
	 * @param File $file
	 */
	public function __construct(File $file) {
		$this->file = $file;
		$this->intervention = new Intervention($file->getRealPath());
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->intervention->width;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->intervention->height;
	}

	/**
	 * @return string
	 */
	public function getAveragePixelColor() {
		$this->intervention->resize(1,1);
		$color = substr($this->intervention->pickColor(0,0, 'hex'), 1);
		$this->intervention->reset();
		return $color;
	}

}