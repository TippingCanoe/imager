<?php namespace TippingCanoe\Imager\Processing;

use Symfony\Component\HttpFoundation\File\File;
use TippingCanoe\Imager\Model\Image;


class CropEntropy implements Filter {

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

	/**
	 * @param File $file
	 * @param Image $image
	 */
	public function process(File $file, Image $image) {
		$cropEntropy = new \stojg\crop\CropEntropy($file->getRealPath());
		$croppedImage = $cropEntropy->resizeAndCrop($this->width, $this->height);
		$croppedImage->writeimage($file->getRealPath());
	}

}