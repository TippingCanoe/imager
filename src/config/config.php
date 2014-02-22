<?php return [

	// Multiple storage options.
	'storage' => [

		'TippingCanoe\Imager\Storage\Filesystem' => [

			// Directory that Imager can manage everything under.
			'root' => public_path() . '/imager',

			// Public, client-accessible prefix pointing to wherever the root is hosted, including scheme.
			'public_prefix' => sprintf('%s/imager', Request::getSchemeAndHttpHost()),

        ],

		// Amazon S3 Storage Driver
		/*
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