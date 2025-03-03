<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'start_time' => 'required|date',
			'end_time' => 'required|date|after:start_time',
			'location' => 'required|string|max:255',
			'capacity' => 'required|integer|min:1',
			'status' => 'required|in:open,closed,canceled'
		];
	}
}
