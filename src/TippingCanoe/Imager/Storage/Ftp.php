<?php namespace TippingCanoe\Imager\Storage;


class Ftp /* implements Driver */ {

/*
 * OLD FTP STORAGE CODE.

		//
		// FTP Storage
		//
		if(isset($this->config['storage']['ftp']) && is_array($this->config['storage']['ftp'])) {

			$ftpClient = new FtpClient();
			$ftpClient->connect(
				$this->config['storage']['ftp']['host'],
				false,
				$this->config['storage']['ftp']['port']
			);
			$ftpClient->login(
				$this->config['storage']['ftp']['username'],
				$this->config['storage']['ftp']['password']
			);

			$ftpClient->passive();

			foreach($images as $imagePath => $imageFile) {

				$saveFile = sprintf('%s/%s', $this->config['storage']['ftp']['path'], $imagePath);

				$dir = dirname($saveFile);

				try {
					$ftpClient->changeDirectory($dir);
					$dirExists = true;
				}
				catch(Exception $e) {
					$dirExists = false;
				}

				$ftpClient->changeDirectory('/');

				if(!$dirExists){
					$ftpClient->createDirectory($dir);
				}

				$ftpClient->put($saveFile, $imageFile, FTP_BINARY);
			}
		}

 */

}