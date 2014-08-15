<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManager as Intervention;


class Blur implements Filter {

	/** @var string */
	protected $sourcePath;

	protected $brightness = -10;

	protected $blur = 30;

	protected $compression = 100;

	protected $intervention;

	public function __construct(Intervention $intervention) {
		$this->intervention = $intervention;
	}
	/**
	 * @param string $sourcePath
	 */
	public function setSourcePath($sourcePath) {
		$this->sourcePath = $sourcePath;
	}

	public function setBlur($value) {
		$this->blur = $value;
	}

	public function setBrightness($value) {
		$this->brightness = $value;
	}

	public function setCompression($value) {
		$this->compression = $value;
	}

	public function process(File $file, Image $image) {
		$imageData = $this->intervention->make($file->getRealPath());

		$imageData->brightness($this->brightness);
		$imageData->blur($this->blur);
		$imageData->save(null, $this->compression);
	}

}
