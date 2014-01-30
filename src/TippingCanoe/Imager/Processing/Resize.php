<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\Image as Intervention;


class Resize implements Filter {

	/** @var int */
	protected $width;

	/** @var int */
	protected $height;

	/** @var boolean */
	protected $preserveRatio;

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

	/**
	 * @param boolean $preserveRatio
	 */
	public function setPreserveRatio($preserveRatio) {
		$this->preserveRatio = $preserveRatio;
	}

	public function process(File $file, Image $image, array $config = null) {

		$imageData = new Intervention($file->getRealPath());

		$imageData->resize($this->width, $this->height, $this->preserveRatio);

		$imageData->save(null, 100);

	}

}