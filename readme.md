# Imager

Have you ever wanted to smooth away the hassle of managing uploaded images in your Laravel 4 projects?  Do you want to simplify the process of modifying and caching originally uploaded images?  Look no further!  Imager is a package designed to ease management of image storage, order and manipulation.

Features at a [glance](https://docs.google.com/presentation/d/1BzFZSpVx7qqcOK8QOR-MtTjY2_KMA2rd6Un-a7WFvu0):

 * Attach images to any eloquent model via an interface and an optionally supplied trait
 * Configurable and customizable storage drivers
 * Generate URIs for images based on which storage driver is in use
 * Image processing chains & caching
 * A powerful and agnostic gallery batch processor


## Setup

To get Imager ready for use in your project, take the usual steps for setting up a Laravel 4 pacakge.

 * Add `tippingcanoe/imager` to your `composer.json` file.
 * Run `composer update` at the root of your project.
 * Edit your `app/config/app.php` file and add: 
   * `'TippingCanoe\Imager\ServiceProvider',` into the `providers` array 
   * `'Imager' => 'TippingCanoe\Imager\Facade',` into the `aliases` array
 * Run the migration `./artisan migrate --package="tippingcanoe/imager"`
 * Take a project-level copy of the configuration `./artisan config:publish tippingcanoe/imager`

```
Note: If you are using type-hinted dependency injection in your project, as a convenience Imager also binds the type `TippingCanoe\Imager\Service` in the container.
```

## Configuring

### Storage
If you open the copy of `config.php` that was created by the last step during setup, you will see it is already populated with configuration options for the most typical of setups.  The `TippingCanoe\Imager\Storage\Filesystem` driver is the most basic which simply stores image files in your site's public directory.

### Filters
Imager's filtering chains are a powerful feature that allow you to orchestrate arbitrary combinations of manipulations when retrieving images.  When processing a chain, Imager does the following for each filter in the chain:

 * Attempts to instantiate the indicated class which must implement `TippingCanoe\Imager\Processing\Filter`
 * If the filter's configuration was an array, each key in the second index will be called as setter methods on the subclass
 * Call the method `process` on the subclass passing in the original image and the database entry for the image
 
You will most likely want to pre-configure filter chains for your project so that you don't have to repeat them over the course of retrieving variations.  Imager uses a simple array schema to define filtering chains.  Here's a sample of one:

```
[

	'TippingCanoe\Imager\Processing\FixRotation',
	
	[
		'TippingCanoe\Imager\Processing\Resize',
		[
			'width' => 300,
			'height' => 300,
			'preserve_ratio' => true
		]
	],
	
	[
		'TippingCanoe\Imager\Processing\Watermark',
		[
			'source_path' => sprintf('%s/logo.png', __DIR__),
			'anchor' => 'bottom-right'
		]
	]

]
```

 * The array must not be keyed.
 * An entry that is a string will be instantiated and run without parameters.
 * An entry that is a sub-array will have the first index `[0]` of that array instantiated with the second index `[1]` converted to setters on the instance:
   * setWidth(300)
   * setHeight(300)
   * setPreserveRatio(true)
   * etc...

If you're unsure as to where you should store your filter profiles, it's suggested that you place them in the `filters.php` file that has also been created for you when you published Imager's configuration earlier.  This will allow you to vary the filter configurations along with your environments and will make retrieval as simple as `Config::get('imager::filters.filter_name')`


## Usage

Depending on the nature of your implementation, the means by which you will receive image files will vary.  Imager makes no assumptions about your request lifecycle (or that there's even a request at all!) and only concerns itself with recieving instances of `Symfony\Component\HttpFoundation\File\File`.

The two optional, secondary pieces of information that Imager makes use of are `Imageable` to scope to a specific model and filter chains during retrieval.


### Trait

If you plan on attaching images to a model (User, Item, Gallery), you must implement the interface `TippingCanoe\Imager\Model\Imageable` on that model.  This will mandate a method that you can either implement yourself or conveniently keep in sync with Imager by using the trait `TippingCanoe\Imager\Model\ImageableImpl`.


### Saving

Saving images is done via the Imager service which can either be accessed via the facade or through dependency injection.

```
	/** @var \Symfony\Component\HttpFoundation\File\File $file */
	/** @var \TippingCanoe\Imager\Model\Imageable $imageable */

	$attributes = [
		'slot' => 1
	];

	/** @var \TippingCanoe\Imager\Model\Image $image */
	$image = Imager::saveFromFile($file, $imageable, $attributes);
```

Imager will return an instance of `TippingCanoe\Imager\Model\Image` upon a successful save, if you supplied one, the image record will be associated with the imageable you supplied and any additional attributes will be passed through to the save as well.

### Retrieval

When retrieving an image, you will need a way to identify it:

 * The image's `id`
 * The image's slot
 * An imageable's `images()` relation

Most of the time you will have at least one of these three pieces of information which will then allow you to obtain a URI to the physical file of the image.

```
	Imager::getPublicUri($image, $filters);
	Imager::getPublicUriBySlot($slot, $imageable, $filters);
	Imager::getPublicUriById($id, $filters);
```

When retrieving images from imager, it's helpful to remember that anywhere you see "imageable" is optional and omitting it or providing null it means _"global"_.  Similarly, "filters" is also optional and omitting this value, providing null or an empty array will mean _"the original image"_.


### Slots

Imager feature a concept known as slots which at it's very core is just a string value.  Slots are used to order and/or key images by their imageable.  There are helper scopes on the `TippingCanoe\Imager\Model\Image` class to help with retrieving images based on their slot values.

```
Note: When storing images without an imageable (globally), keep in mind that they are all sharing the same slot scope and cannot have duplicates.
```

A sample use case for slots would be an "Item" class that can have an image gallery as well as a "primary" image.  Images belonging to the gallery would have slots that are numeric so that they can be kept in a specific order while the primary image is in a named slot that can be queried directly.


#### Batches

It's common for implementations to require a way to submit multiple changes to an imageable's images in a single pass.  These changes can sometimes present conflicts and be challenging to resolve.

As a convenience, Imager supplies a batch method off the service that allows these bulk operations to be performed.  The operations are scoped by imageable and performed by-slot in a safe order.

The structure of the schema is caller agnostic and in the unavoidable case of a conflict will null-out the slot of any images being displaced.

Here's a sample of the schema used when performing batch operations:

```
	$operations = [
		1 => 2,
		2 => 'thumbnail',
		3 => null,
		4 => 'http://placehold.it/200x200&text=Imager'
	];

	/** @var \Symfony\Component\HttpFoundation\File\File[] $newFiles */
	/** @var \TippingCanoe\Imager\Model\Imageable $imageable */

	Imager::batch($operations, $newFiles, $imageable);

```
In this example, the following actions would be taken - in order:

 * The images in slot 1 and 2 would be swapped
 * The image in slot 2 would be moved to the 'thumbnail' slot
 * The image in slot 3 will be deleted
 * The image found at the URL will be downloaded and inserted into slot 3
 
The file array `$newFiles` will be keyed by slot and could in theory contain new images for slots 1 and 3.  

When an image is told to move to a new slot, if there is an image in the target slot, they will swap.  If an uploaded image attempts to go into an already-occupied slot, the image currently in the slot will have it's slot nulled out.

It's important to note that slot keys cannot be duplicated, so it's in your best interest to submit **the simplest** batch list possible.


## Drivers

More drivers will be added over time and we are always interested in hearing suggestions for new ones or receiving pull requests with your own ideas.  Creating a driver is as simple as implementing the interface `TippingCanoe\Imager\Storage\Driver` which is fully documented.  You can also use `TippingCanoe\Imager\Storage\Filesystem` as a reference.


## Filters

It's very easy to create your own filters within your own project or packages.  You're also more than welcome to use whatever image processing libraries and/or algorithms you wish.

The only rule is that filter subclasses must perform their manipulations to the file provided without moving, renaming or deleting it - overwriting is fine.  The `process` method is not expected to return a value.


## Meta

If you encounter any issues, find a bug or have any questions, feel free to open a ticket in the issue tracker.


### Credits

Imager is created and maintained by [Alexander Trauzzi](http://github.com/atrauzzi) at [Tipping Canoe](http://www.tippingcanoe.com).