<?php namespace TippingCanoe\Imager\Processing;

use Symfony\Component\HttpFoundation\File\File;
use TippingCanoe\Imager\Model\Image;


interface Filter {

	public function process(File $file, Image $image);

}