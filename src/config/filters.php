<?php return [

	// Here's a sample image filter for you to use.  Create as many as your project requires!
	'shrink' => [

		'TippingCanoe\Imager\Processing\FixRotation',

		[
			'TippingCanoe\Imager\Processing\Resize',
			[
				'width' => 300,
				'height' => 300,
				'preserve_ratio' => true
			]
		],

	]

];