<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * * @method static Builder create(array $attributes = [])
 * * @method static Builder find(string $value)
 * * @method public Builder update(array $values)
 */
class Notification extends Model
{
	use HasUuids, HasFactory, SoftDeletes;

	protected $table = 'notifications';

	protected $fillable = [
		'type',
		'data',
		'read_at',
		'notifiable_id',
		'notifiable_type'
	];

	protected array $dates = [
		'created_at',
		'updated_at'
	];

	protected $hidden = [];

	protected $primaryKey = 'id';
	protected $keyType = 'string';

	public $timestamps = true;
	public $incrementing = false;

}
