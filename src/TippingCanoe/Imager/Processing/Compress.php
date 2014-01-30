<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\Image as Intervention;

class Compress implements Filter {

	/** @var int */
	protected $quality;

	/**
	 * @param int $quality
	 */
	public function setQuality($quality) {
		$this->quality = $quality;
	}

	public function process(File $file, Image $image, array $config = null) {

		$imageData = new Intervention($file->getRealPath());

		$imageData->save(null, $this->quality);

	}

}