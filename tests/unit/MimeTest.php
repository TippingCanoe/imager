<?php

use TippingCanoe\Imager\Mime;


class MimeTest extends \Codeception\TestCase\Test {
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

    public function test_it_gets_supported_mime_types()
    {
     $mime = new Mime();

     $imageTypes = $mime->getTypes();

     // Supported Image Types
     $this->assertArrayHasKey('image/gif', $imageTypes);
     $this->assertArrayHasKey('image/jpeg', $imageTypes);
     $this->assertArrayHasKey('image/png', $imageTypes);

     // Unsupport image types
     $this->assertArrayNotHasKey('image/bmp', $imageTypes);
     $this->assertArrayNotHasKey('image/tiff', $imageTypes);
    }

 public function test_it_gets_image_extension_by_mime_type()
 {
  $mime = new Mime();

  $jpegExtension = $mime->getExtensionForMimeType('image/jpeg');

  $pngExtension = $mime->getExtensionForMimeType('image/png');

  $gifExtension = $mime->getExtensionForMimeType('image/gif');

  $this->assertEquals('jpg', $jpegExtension);
  $this->assertEquals('png', $pngExtension);
  $this->assertEquals('gif', $gifExtension);
 }

}
