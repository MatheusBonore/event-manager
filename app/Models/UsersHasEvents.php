<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;

/**
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder create(array $attributes = [])
 * @method static Builder find(string $value)
 * @method public Builder update(array $values)
 */
class UsersHasEvents extends Model
{
	use HasFactory, Notifiable, softDeletes;

	protected $table = 'users_has_events';
	protected $fillable = [
		'users_user',
		'events_event'
	];

	protected array $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];

	protected $hidden = [];
	protected $primaryKey = [
		'users_user',
		'events_events'
	];
	protected $keyType = 'sting';

	public $timestamps = true;
	public $incrementing = false;

	public static array $rules = [];

	public function event(): BelongsTo
	{
		return $this->belongsTo(Event::class, 'events_event');
	}
}