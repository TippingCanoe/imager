<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic as Intervention;

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

		$imageData = Intervention::make($file->getRealPath());

		$imageData->save(null, $this->quality);

	}

}
