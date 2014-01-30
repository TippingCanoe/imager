<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateImage extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create('imager_image', function (Blueprint $table) {

			$table
				->increments('id')
				->unsigned()
			;

			$table
				->integer('imageable_id')
				->unsigned()
				->nullable()
			;
			$table
				->string('imageable_type')
				->nullable()
			;

			$table
				->string('slot')
				->nullable()
			;

			$table
				->integer('width')
				->unsigned()
			;

			$table
				->integer('height')
				->unsigned()
			;

			$table->string('mime_type');

			$table->string('average_color', 6);

			$table->timestamps();

			//
			// Indexes
			//

			$table->unique(['imageable_id', 'imageable_type', 'slot'], 'U_imageable_slot');

		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('imager_image');
	}

}