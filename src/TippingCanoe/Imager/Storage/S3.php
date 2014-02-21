<?php namespace TippingCanoe\Imager\Storage;

use Symfony\Component\HttpFoundation\File\File;
use TippingCanoe\Imager\Model\Image;
use TippingCanoe\Imager\Mime;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Enum\CannedAcl;

class S3 implements Driver{

    /**
     * @var Aws\S3\S3Client
     */
    protected $s3;

    /**
     * @var string
     */
    protected $awsBucket;

    /**
     * @var string
     */
    private $awsKey;

    /**
     * @var string
     */
    private $awsSecret;


    /**
     * @param string $key
     */
    public function setAwsKey($key)
    {
        $this->awsKey = $key;
    }

    /**
     * @param $secret
     */
    public function setAwsSecret($secret)
    {
        $this->awsSecret = $secret;
    }

    /**
     * Build the S3 Client
     */
    public function __construct()
    {
        // @todo get aws configuration from config file
        $this->awsBucket = '';
        $this->awsKey = '';
        $this->awsSecret = '';

        $this->s3 = S3Client::factory(array(
            'key'    => $this->awsKey,
            'secret' => $this->awsSecret,
        ));

    }

    /**
     * Saves an image.
     *
     * Exceptions can provide extended error information and will abort the save process.
     *
     * @param File $file
     * @param Image $image
     * @param array $filters
     */
    public function saveFile(File $file, Image $image, array $filters = [])
    {
        // Upload a file.
        try {
            $this->s3->putObject(array(
                'Bucket'        => $this->awsBucket,
                'Key'           => $this->generateFileName($image, $filters),
                'SourceFile'    => $file->getRealPath(),
                'ACL'           => CannedAcl::PRIVATE_ACCESS,
            ));
        } catch (S3Exception $e) {
            echo "There was an error uploading the file.\n";
        }
    }

    /**
     * Returns the public URI for an image by a specific configuration.
     *
     * @param Image $image
     * @param array $filters
     * @return string
     */
    public function getPublicUri(Image $image, array $filters = [])
    {
        //dd($this->generateFileName($image, $filters));
        try
        {
            return $this->s3->getObjectUrl($this->awsBucket, $this->generateFileName($image, $filters), '+10 minutes');
        } catch (S3Exception $e) {
            echo "There was an error generating the file url.\n";
        }
    }

    /**
     * Asks the driver if it has a particular image.
     *
     * @param \TippingCanoe\Imager\Model\Image $image
     * @param array $filters
     * @return boolean
     */
    public function has(Image $image, array $filters = [])
    {
        return $this->s3->doesObjectExist(
            $this->awsBucket,
            $this->generateFileName($image, $filters));
    }

    /**
     * Tells the driver to delete an image.
     *
     * Deleting must at least ensure that afterwards, any call to has() returns false.
     *
     * @param Image $image
     * @param array $filters
     */
    public function delete(Image $image, array $filters = [])
    {
        // Upload a file.
        try {
            $this->s3->deleteObject(array(
                'Bucket' => $this->awsBucket,
                'Key'    => $this->generateFileName($image, $filters),
            ));
        } catch (S3Exception $e) {
            echo "There was an error deleting the file.\n";
        }
    }

    /**
     * Tells the driver to prepare a copy of the original image locally.
     *
     * @param Image $image
     * @return File
     */
    public function tempOriginal(Image $image)
    {
        $tempOriginalPath = tempnam(sys_get_temp_dir(), null);
        $originalPath = sprintf('%s-%s.%s',
            $image->getKey(),
            $this->generateHash($image),
            Mime::getExtensionForMimeType($image->mime_type)
        );
        try {
            $this->s3->getObject(array(
                'Bucket' => $this->awsBucket,
                'Key'    => $originalPath,
                'SaveAs' => $tempOriginalPath
            ));
            return new File($tempOriginalPath);
        } catch (S3Exception $e) {
            echo "There was an error deleting the file.\n";
        }
    }

    /**
     * @param Image $image
     * @param array $filters
     * @return string
     */
    protected function generateFileName(Image $image, array $filters = []) {
        return sprintf('%s-%s.%s',
            $image->getKey(),
            $this->generateHash($image, $filters),
            Mime::getExtensionForMimeType($image->mime_type)
        );
    }

    /**
     * Generates a hash based on an image and it's filters.
     *
     * @param Image $image
     * @param array $filters
     * @return string
     */
    protected function generateHash(Image $image, array $filters = []) {

        $state = [
            'id' => (string)$image->getKey(),
            'filters' => $filters
        ];

        // Must be recursively sorted otherwise arrays with similar keys in different orders won't have the same hash!
        $state = $this->recursiveKeySort($state);

        return md5(json_encode($state));
    }

    /**
     * Utility method to ensure that key signatures always appear in the same order.
     *
     * @param array $array
     * @return array
     */
    protected function recursiveKeySort(array $array) {

        ksort($array);

        foreach($array as $key => $value)
            if(is_array($value))
                $array[$key] = $this->recursiveKeySort($value);

        return $array;

    }
}