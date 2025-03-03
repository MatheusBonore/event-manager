<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
	public function toArray($request)
	{
		return [
			'event' => $this->event,
			'title' => $this->title,
			'description' => $this->description,
			'start_time' => $this->start_time,
			'end_time' => $this->end_time,
			'location' => $this->location,
			'capacity' => $this->capacity,
			'status' => $this->status,
			'creator' => new UserResource($this->whenLoaded('creator')),
			'attendees' => UserResource::collection($this->whenLoaded('attendees')),
		];
	}
}
