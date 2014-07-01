<?php namespace TippingCanoe\Imager\Processing;

use TippingCanoe\Imager\Model\Image;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic as Intervention;


class FixRotation implements Filter {

	public function process(File $file, Image $image, array $config = null) {

		// Only jpeg images support EXIF.
		if($image->mime_type != 'image/jpeg')
			return;

		$imageData = Intervention::make($file->getRealPath());

		switch($imageData->exif('Orientation')) {

			case 8:
				$imageData->rotate(90);
			break;

			case 3:
				$imageData->rotate(180);
			break;

			case 6:
				$imageData->rotate(-90);
			break;

		}

		$imageData->save(null, 100);

	}

}
