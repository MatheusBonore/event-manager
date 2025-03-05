<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * * @method static Builder create(array $attributes = [])
 * * @method static Builder find(string $value)
 * * @method public Builder update(array $values)
 */
class Event extends Model
{
	use HasUuids, HasApiTokens, SoftDeletes, HasFactory, Notifiable;

	protected $table = 'events';

	protected $fillable = [
		'users_user',
		'title',
		'description',
		'start_time',
		'end_time',
		'location',
		'capacity',
		'status'
	];

	protected array $dates = [
		'created_at',
		'updated_at',
		'deleted_at'
	];

	protected $hidden = [];

	protected $primaryKey = 'event';
	protected $keyType = 'string';

	public $timestamps = true;
	public $incrementing = false;

	protected $casts = [
		'start_time' => 'datetime',
		'end_time' => 'datetime'
	];

	public function creator()
	{
		return $this->belongsTo(User::class, 'users_user', 'user');
	}

	public function attendees()
	{
		return $this->belongsToMany(User::class, 'users_has_events', 'events_event', 'users_user');
	}

	public function confirmedAttendees()
	{
		return $this->belongsToMany(User::class, 'users_has_events', 'events_event', 'users_user')
			->wherePivot('confirmed', true);
	}

	public function unconfirmedAttendees()
	{
		return $this->belongsToMany(User::class, 'users_has_events', 'events_event', 'users_user')
			->wherePivot('confirmed', false);
	}
}
