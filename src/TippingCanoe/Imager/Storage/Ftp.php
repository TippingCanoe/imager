<?php namespace TippingCanoe\Imager\Storage;

use Symfony\Component\HttpFoundation\File\File;
use TippingCanoe\Imager\Mime;
use TippingCanoe\Imager\Model\Image;
use FtpPhp\FtpClient;

class Ftp extends Base {

	protected $ftp;

	protected $root;

	protected $publicUrl;

	public function setRoot($value) {
		$this->root = $value;
	}

	public function setConnection($value) {
		$this->ftp = new FtpClient($value);
	}

	public function setPublicUrl($value) {
		$this->publicUrl = $value;
	}

	public function saveFile(File $file, Image $image, array $filters = []){

		$this->ftp->put($this->root . '/'. $this->generateFileName($image, $filters), $file->getRealPath() );
	}

	public function tempOriginal(Image $image) {

		// Recreate original filename
		$tempOriginalPath = tempnam(sys_get_temp_dir(), null);

		$originalPath = sprintf('%s-%s.%s',
			$image->getKey(),
			$this->generateHash($image),
			Mime::getExtensionForMimeType($image->mime_type)
		);

		// Download file
		$this->ftp->fget($tempOriginalPath, $originalPath, FtpClient::ASCII);

		return new File($tempOriginalPath);

	}

	public function getPublicUri(Image $image, array $filters = []){
		return sprintf('%s%s', $this->publicUrl, $this->generateFileName($image, $filters));
	}

	public function delete(Image $image, array $filters = []){
		$this->ftp->tryDelete($this->generateFileName($image, $filters));
	}

	public function has(Image $image, array $filters = []) {
		return $this->ftp->fileExists($this->generateFileName($image, $filters));
	}
}