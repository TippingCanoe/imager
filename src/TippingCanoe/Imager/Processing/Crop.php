<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\Image as Intervention;

/**
 * Class Crop
 *
 * Allows cropping of an image. Defaults to center of image.
 *
 * Can be given a relative starting point for the top-left origin of the crop to begin.
 *
 * @package TippingCanoe\Imager\Processing
 */
class Crop implements Filter {

	/** @var array */
	protected $sections = [
		'center',
		'top-left',
		'bottom-right',
		'top',
		'bottom',
		'bottom-left',
		'bottom-right',
		'left',
		'right'
	];

	/** @var int */
	protected $width;

	/** @var int */
	protected $height;

	/** @var int */
	protected $originLeft;

	/** @var int */
	protected $originTop;

	/** @var string */
	protected $section;

	/**
	 * @param string $section
	 * @throws \Exception
	 */
	public function setSection($section) {
		$this->section = $section;
		throw new \Exception('Not yet implemented!');
	}

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
	 * @param int $originLeft
	 */
	public function setOriginLeft($originLeft) {
		$this->originLeft = $originLeft;
	}

	/**
	 * @param int $originTop
	 */
	public function setOriginTop($originTop) {
		$this->originTop = $originTop;
	}

	/**
	 * @param boolean $preserveRatio
	 */
	public function setPreserveRatio($preserveRatio) {
		$this->preserveRatio = $preserveRatio;
	}

	public function process(File $file, Image $image, array $config = null) {

		$imageData = new Intervention($file->getRealPath());

		// Sections override origin data.
		if($this->section)
			// ToDo: Add parameter to calculate crops by section.
			return;
		else
			$imageData->crop($this->width, $this->height, $this->originTop, $this->originLeft);

		$imageData->save(null, 100);

	}

}