<?php namespace TippingCanoe\Imager\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Image extends Model {

	protected $table = 'imager_image';

    	protected $fillable = [
		'imageable_id', 
		'imageable_type',
		'slot',
		'width',
		'height',
		'average_color',
		'mime_type'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function imageable() {
		return $this->morphTo();
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $type
	 * @param int $id
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeForImageable(Builder $query, $type, $id) {
		return $query
			->where('imageable_type', $type)
			->where('imageable_id', $id)
		;
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $slot
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeInSlot(Builder $query, $slot) {
		return $query->whereIn('slot', (array)$slot);
	}

	/**
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param string $slot
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeNotInSlot(Builder $query, $slot) {
		return $query->whereNotIn('slot', (array)$slot);
	}

	/**
	 * @param Builder $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWithoutSlot(Builder $query) {
		return $query->whereNull('slot');
	}

	/**
	 * Modifies the query to only include images without imageables.
	 *
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeUnattached(Builder $query) {
		return $query
			->whereNull('imageable_id')
			->whereNull('imageable_type')
		;
	}

	/**
	 * Modifies the query to only include images attached to an imageable.
	 *
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeAttached(Builder $query) {
		return $query
			->whereNotNull('imageable_id')
			->whereNotNull('imageable_type')
		;
	}

	/**
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeHighestRes(Builder $query) {
		return $query->orderByRaw('(width * height) DESC');
	}

	/**
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeRandom(Builder $query) {
		return $query->orderBy('RAND()');
	}

	/**
	 * Only retrieve images whose slots are integers.
	 *
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeInIntegerSlot(Builder $query) {
		return $query->whereRaw(sprintf('%s.slot REGEXP \'^[[:digit:]]+$\'', $query->getQuery()->from));
	}

	public function scopeInNamedSlot() {
		/* ToDo: Implement */
	}

	public function scopeOnlyPortrait() {
		/* ToDo: Implement */
	}

	public function scopeOnlyLandscape() {
		/* ToDo: Implement */
	}

	public function scopeWithMinimumWidth() {
		/* ToDo: Implement */
	}

	public function scopeWithMinimumHeight() {
		/* ToDo: Implement */
	}

}
