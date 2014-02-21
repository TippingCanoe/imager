<?php return [

	// Multiple storage options.
	'storage' => [

		'TippingCanoe\Imager\Storage\Filesystem' => [

			// Directory that Imager can manage everything under.
			'root' => public_path() . '/imager',

			// Public, client-accessible prefix pointing to wherever the root is hosted, including scheme.
			'public_prefix' => sprintf('%s/imager', Request::getSchemeAndHttpHost()),

        ],
        /* Use this configuration for the Amazon S3 Driver
        'TippingCanoe\Imager\Storage\S3' => [
            'aws_key' => 'YOUR_KEY_HERE',
            'aws_secret' => 'YOUR_SECRET_HERE',
            'aws_bucket' => 'imager-bucket',
        ],
        */
    ],

];