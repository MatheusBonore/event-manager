<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

/**
 * * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * * @method static Builder create(array $attributes = [])
 * * @method static Builder find(string $value)
 * * @method public Builder update(array $values)
 */
class Event extends Model
{
	use HasUuids, HasFactory, Notifiable;

	protected $table = 'events';

	protected $fillable = [
		'title',
		'description',
		'location',
		'capacity',
		'status',
	];

	protected array $dates = [
		'created_at',
		'updated_at',
	];

	protected $hidden = [];

	protected $primaryKey = 'event';
	protected $keyType = 'string';

	public $timestamps = true;
	public $incrementing = false;

	protected $casts = [
		'start_time' => 'datetime',
		'end_time' => 'datetime',
	];
}
