<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'sometimes|string|max:255',
			'description' => 'sometimes|string',
			'start_time' => 'sometimes|date',
			'end_time' => 'sometimes|date|after:start_time',
			'location' => 'sometimes|string|max:255',
			'capacity' => 'sometimes|integer|min:1',
			'status' => 'sometimes|in:open,closed,canceled',
		];
	}
}
