<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\StoreEventRequest;
use App\Http\Requests\Api\UpdateEventRequest;
use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
	public function index(Request $request): JsonResponse
	{
		$allowedIncludes = ['creator', 'attendees'];

		// Obter os valores do ?include=
		$includes = $request->query('include') ? explode(',', $request->query('include')) : [];

		$validIncludes = array_intersect($includes, $allowedIncludes);
		$invalidIncludes = array_diff($includes, $allowedIncludes);

		// Se houver includes inválidos, retornar erro
		if (!empty($invalidIncludes)) {
			return response()->json([
				'success' => false,
				'message' => 'The following values provided in "include" are invalid.',
				'invalid' => array_values($invalidIncludes),
				'allowed' => $allowedIncludes
			], 400);
		}

		$events = Event::with($validIncludes)->get();

		return response()->json([
			'success' => true,
			'data' => EventResource::collection($events)
		]);
	}

	public function store(StoreEventRequest $request): JsonResponse
	{
		$event = array_merge($request->validated(), ['users_user' => Auth::user()->user]);

		return response()->json([
			'success' => true,
			'data' => Event::create($event)
		], 201);
	}

	public function show(string $id, Request $request): JsonResponse
	{
		$allowedIncludes = ['creator', 'attendees'];

		// Obter os valores do ?include=
		$includes = $request->query('include') ? explode(',', $request->query('include')) : [];

		$validIncludes = array_intersect($includes, $allowedIncludes);
		$invalidIncludes = array_diff($includes, $allowedIncludes);

		// Se houver includes inválidos, retornar erro
		if (!empty($invalidIncludes)) {
			return response()->json([
				'success' => false,
				'message' => 'The following values provided in "include" are invalid.',
				'invalid' => array_values($invalidIncludes),
				'allowed' => $allowedIncludes
			], 400);
		}

		$event = Event::with($validIncludes)->find($id);
		if (!$event) {
			return response()->json([
				'success' => false,
				'message' => 'Event not found'
			], 404);
		}

		return response()->json([
			'success' => true,
			'data' => new EventResource($event)
		]);
	}

	public function update(UpdateEventRequest $request, string $id): JsonResponse
	{
		$event = Event::find($id);
		if (!$event) {
			return response()->json([
				'success' => false,
				'message' => 'Event not found'
			], 404);
		}

		$event->update($request->validated());
		return response()->json([
			'success' => true,
			'data' => $event
		]);
	}

	public function destroy(string $id): JsonResponse
	{
		$event = Event::find($id);
		if (!$event) {
			return response()->json([
				'success' => false,
				'message' => 'Event not found'
			], 404);
		}

		$event->delete();
		return response()->json([
			'success' => true,
			'message' => 'Event deleted successfully'
		]);
	}
}