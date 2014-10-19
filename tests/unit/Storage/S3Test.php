<?php

use TippingCanoe\Imager\Storage\S3;
use Mockery as M;

class S3Test extends \Codeception\TestCase\Test {

   /**
    * @var \UnitTester
    */
    protected $tester;

    protected $bucketName = 'testBucket';

    public function test_it_saves_a_file()
    {

     $s3Client = M::mock('Aws\S3\S3Client');
     $image = M::mock('TippingCanoe\Imager\Model\Image');
     $file = M::mock('Symfony\Component\HttpFoundation\File\File');

     $s3 = new S3($s3Client);
     $s3->setBucket($this->bucketName);

     // @todo check that the right args are provided to putObject on s3 client
     $s3Client->shouldReceive('putObject')->withAnyArgs()->once();
     $file->shouldReceive('getRealPath')->once()->andReturn('/some/fake/path.jpg');
     $image->shouldReceive('getKey')->times(2)->andReturn(1);
     $image->shouldReceive('getAttribute')->with('mime_type')->andReturn('image/jpeg');

     $s3->saveFile($file, $image, []);
    }

    public function test_it_gets_an_images_public_url()
    {
     // @todo
    }

    public function test_it_checks_to_see_if_an_image_exists()
    {
     // @todo
    }

    public function test_it_deletes_an_image()
    {
     // @todo
    }

    public function test_it_downloads_the_original_image()
    {
     // @todo
    }

}
