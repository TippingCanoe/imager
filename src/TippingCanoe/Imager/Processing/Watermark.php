<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic as Intervention;


class Watermark implements Filter {

	/** @var string */
	protected $sourcePath;

	/** @var int */
	protected $offsetLeft;

	/** @var int */
	protected $offsetRight;

	/** @var string */
	protected $anchor;

	/**
	 * @param string $sourcePath
	 */
	public function setSourcePath($sourcePath) {
		$this->sourcePath = $sourcePath;
	}

	/**
	 * @param int $offsetLeft
	 */
	public function setOffsetLeft($offsetLeft) {
		$this->offsetLeft = $offsetLeft;
	}

	/**
	 * @param int $offsetRight
	 */
	public function setOffsetRight($offsetRight) {
		$this->offsetRight = $offsetRight;
	}

	/**
	 * @param string $anchor
	 */
	public function setAnchor($anchor) {
		$this->anchor = $anchor;
	}

	public function process(File $file, Image $image, array $config = null) {

		$imageData = Intervention::make($file->getRealPath());

		$imageData->insert(
			$this->sourcePath,
			$this->offsetLeft,
			$this->offsetRight,
			$this->anchor
		);

		$imageData->save(null, 100);

	}

}
