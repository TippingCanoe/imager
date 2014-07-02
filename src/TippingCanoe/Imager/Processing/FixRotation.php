<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic as Intervention;


class FixRotation implements Filter {

	public function process(File $file, Image $image, array $config = null) {

		$imageData = Intervention::make($file->getRealPath());

		$imageData->orientate();

		$imageData->save(null, 100);

	}

}
