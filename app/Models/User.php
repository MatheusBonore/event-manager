<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

/**
 * * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * * @method static Builder create(array $attributes = [])
 * * @method static Builder find(string $value)
 * * @method public Builder update(array $values)
 */
class User extends Authenticatable
{
	use HasUuids, HasFactory, Notifiable;

	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
		'role',
	];

	protected array $dates = [
		'created_at',
		'updated_at',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $primaryKey = 'user';
	protected $keyType = 'string';

	public $timestamps = true;
	public $incrementing = false;

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	public function events()
	{
		return $this->belongsToMany(Event::class, 'users_has_events', 'users_user', 'events_event');
	}
}
