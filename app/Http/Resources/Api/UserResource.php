<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'user' => $this->user,
			'name' => $this->name,
			'email' => $this->email
		];
	}
}