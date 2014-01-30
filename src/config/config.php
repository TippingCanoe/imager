<?php return [

	// Multiple storage options.
	'storage' => [

		'TippingCanoe\Imager\Storage\Filesystem' => [

			// Directory that Imager can manage everything under.
			'root' => public_path() . '/imager',

			// Public, client-accessible prefix pointing to wherever the root is hosted, including scheme.
			'public_prefix' => sprintf('%s/imager', Request::getSchemeAndHttpHost()),

		],

	],

];