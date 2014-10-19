<?php

use TippingCanoe\Imager\ImageData;

use Mockery as M;
use Codeception\TestCase\Test;

class ImageDataTest extends Test {
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function test_it_gets_image_dimensions()
    {
     $image = M::mock('Intervention\Image\Image');
     $file = M::mock('Symfony\Component\HttpFoundation\File\File');

     $image->shouldReceive('backup')->once();
     $image->shouldReceive('width')->andReturn(100);
     $image->shouldReceive('height')->andReturn(50);

     $imageData = new ImageData($file, $image);

     $width = $imageData->getWidth();
     $height = $imageData->getHeight();

     $this->assertEquals(100, $width);
     $this->assertEquals(50, $height);

    }

    public function test_it_gets_an_images_average_color()
    {
     $image = M::mock('Intervention\Image\Image');
     $file = M::mock('Symfony\Component\HttpFoundation\File\File');

     $image->shouldReceive('backup')->once();
     $image->shouldReceive('resize')->with(1, 1)->once();
     $image->shouldReceive('reset')->once();

     $image->shouldReceive('pickColor')->once()->andReturn('#000000');

     $imageData = new ImageData($file, $image);

     $averageColor = $imageData->getAveragePixelColor();

     $this->assertEquals('000000', $averageColor);
    }

}
