<?php return [

	/*
	|--------------------------------------------------------------------------
	| Processor for images
	|--------------------------------------------------------------------------
	|
	|  This option controls the default image processor that will be used
	|   when using imager. Of course, you may use other drivers any time you
	|   wish.
	|   Supported: "gd", "imagick"
	*/
	'driver' => 'gd',

	/*
	|--------------------------------------------------------------------------
	| Storage options
	|--------------------------------------------------------------------------
	|
	|   You have to choose one of the supported storage drivers.
	|   Supported: "Filesystem", "Amazon S3", "Ftp"
	*/
	'storage' => [

		'TippingCanoe\Imager\Storage\Filesystem' => [

			// Directory that Imager can manage everything under.
			'root' => public_path() . '/imager',

			// Public, client-accessible prefix pointing to wherever the root is hosted, including scheme.
			'public_prefix' => sprintf('%s/imager', Request::getSchemeAndHttpHost()),

        ],

		/*

		//Ftp
		'TippingCanoe\Imager\Storage\Ftp' => [
			'connection' => 'ftp://user:password@host/',
			'root' => '/imager/', // This directory MUST exist and should finish with "/"
			'public_url' => sprintf('%sstatic.domain.com/imager', Request::getShemeAndHttpHost()),
		],

		// Amazon S3 Storage Driver
		'TippingCanoe\Imager\Storage\S3' => [
			'bucket' => 'imager'
		],

		*/

    ],

	//
	// Amazon S3 Client
	//
	// Uncommenting these lines tells Imager to take care
	// of the Amazon S3 binding in the IoC.
	//
	// It may be that this binding is accomplished elsewhere in your
	// project and if so, you don't need to duplicate it here.
	//
	//'s3' => [
	//	'key' => 'YOUR_KEY_HERE',
	//	'secret' => 'YOUR_SECRET_HERE',
	//]

];