<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\StoreEventRequest;
use App\Http\Requests\Web\UpdateEventRequest;
use App\Models\Event;
use App\Notifications\EventCancellationNotification;
use App\Notifications\EventParticipationNotification;
use App\Traits\GeneratesConfirmationToken;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
	use GeneratesConfirmationToken;

	public function index(Request $request): View
	{
		$creator = $request->query('creator');
		$attendees = $request->query('attendees');

		// ->where('status', 'open')

		$events = Event::with(['creator', 'confirmedAttendees', 'unconfirmedAttendees'])
			->when($creator, function ($query) use ($creator) {
				return $query->where('users_user', $creator);
			})
			->when($attendees, function ($query) use ($attendees) {
				return $query->whereHas('confirmedAttendees', function ($query) use ($attendees) {
					return $query->where('users_user', $attendees);
				})->orWhereHas('unconfirmedAttendees', function ($query) use ($attendees) {
					return $query->where('users_user', $attendees);
				});
			})
			->get()
			->map(function ($event) {
				$this->processEvent($event);
				return $event;
			});

		// Carregar evento
		$event_id = $request->query('event');
		$event = $event_id
			? Event::with(['creator', 'confirmedAttendees', 'unconfirmedAttendees'])
				->when($creator, function ($query) use ($creator) {
					return $query->where('users_user', $creator);
				})
				->when($attendees, function ($query) use ($attendees) {
					return $query->whereHas('confirmedAttendees', function ($query) use ($attendees) {
						return $query->where('users_user', $attendees);
					})->orWhereHas('unconfirmedAttendees', function ($query) use ($attendees) {
						return $query->where('users_user', $attendees);
					});
				})->find($event_id) : [];

		if ($event) {
			$this->processEvent($event);
		}

		$user = $this->getUserInfo();

		return view('event.events', [
			'user' => $user,
			'event_more' => $event,
			'events' => $events
		]);
	}

	private function processEvent($event)
	{
		// Gerar as iniciais do criador
		$event->creator->initials_name = $this->generateInitials($event->creator->name);

		// Gerar as iniciais dos participantes
		$event->attendees->transform(function ($user) {
			$user->initials_name = $this->generateInitials($user->name);
			return $user;
		});

		// Gerar a descrição truncada
		$event->truncated_description = $this->truncateDescription($event->description);

		// Formatar data de inicio do evento
		$event->start_date_formatted = Carbon::parse($event->start_time)->format('l, d F Y H:i');

		// Calcular as datas do evento
		$this->calculateEventDuration($event);
	}

	private function generateInitials($name)
	{
		$parts = explode(" ", $name);
		$initials = "";
		foreach ($parts as $part) {
			$initials .= strtoupper($part[0]);
		}
		return $initials;
	}

	private function truncateDescription($description, $length = 30)
	{
		$pos = strrpos(substr($description . ' ', 0, $length), ' ');
		return strlen($description) > $length
			? substr($description, 0, $pos - 1) . '...'
			: $description;
	}

	private function calculateEventDuration($event)
	{
		$startDate = Carbon::parse($event->start_time);
		$endDate = Carbon::parse($event->end_time);

		if ($startDate->isPast()) {
			// Evento já passou
			$event->event_status = 'Event has already passed';
		} else {
			// Calcular a duração do evento
			$durationInHours = $startDate->diffInHours($endDate);
			$days = floor($durationInHours / 24);
			$hours = $durationInHours % 24;
			$minutes = $startDate->diffInMinutes($endDate) % 60;

			// Construir a string da duração do evento
			$duration = "";

			if ($days > 0) {
				$duration .= "{$days} " . ($days == 1 ? "day" : "days");
			}

			if ($hours > 0) {
				$duration .= ($duration ? " e " : "") . "{$hours} " . ($hours == 1 ? "hour" : "hours");
			}

			if ($minutes > 0 || ($days == 0 && $hours == 0)) {
				$duration .= ($duration ? " e " : "") . "{$minutes} " . ($minutes == 1 ? "minute" : "minutes");
			}

			if (empty($duration)) {
				$duration = "Less than 1 minute";
			}

			$event->event_status = $duration;
		}
	}

	private function getUserInfo()
	{
		$parts = explode(" ", Auth::user()->name);
		$initials = "";
		foreach ($parts as $part) {
			$initials .= strtoupper($part[0]);
		}

		return [
			'user' => Auth::user()->user,
			'name' => Auth::user()->name,
			'initials_name' => $initials,
			'email' => Auth::user()->email
		];
	}

	public function store(StoreEventRequest $request): JsonResponse
	{
		$event = array_merge($request->validated(), [
			'users_user' => Auth::user()->user
		]);

		return response()->json([
			'success' => true,
			'event' => Event::create($event)
		], 201);
	}

	public function participate(string $event): JsonResponse
	{
		$event = Event::findOrFail($event);
		$user = auth()->user();

		if ($event->attendees()->count() >= $event->capacity) {
			return response()->json([
				'success' => false,
				'message' => 'Event capacity reached.'
			], 400);
		}

		if ($event->attendees->contains($user->user)) {
			return response()->json([
				'success' => false,
				'message' => 'You are already participating in this event.'
			], 400);
		}

		$event->attendees()->attach($user->user, ['confirmed' => false]);

		// Enviar notificação
		$user->notify(new EventParticipationNotification($event, $user));

		return response()->json([
			'success' => true,
			'message' => 'You are now participating in the event.'
		], 200);
	}

	public function leave(string $event): JsonResponse
	{
		$event = Event::findOrFail($event);
		$user = auth()->user();

		if (!$event->attendees->contains($user->user)) {
			return response()->json([
				'success' => false,
				'message' => 'You are not participating in this event.'
			], 400);
		}

		// $event->attendees()->detach($user->user);

		// Enviar notificação de cancelamento
		$user->notify(new EventCancellationNotification($event, $user));

		return response()->json([
			'success' => true,
			'message' => 'You left the event.'
		], 200);
	}

	public function update(UpdateEventRequest $request, string $event): JsonResponse
	{
		$event = Event::find($event);
		if (!$event) {
			return response()->json([
				'success' => false,
				'message' => 'Event not found'
			], 404);
		}

		$data = array_merge($request->validated(), [
			'users_user' => Auth::user()->user
		]);

		$event->update($data);
		return response()->json([
			'success' => true,
			'data' => $event
		]);
	}

	public function confirmAction($event, $action, $token): JsonResponse
	{
		$event = Event::findOrFail($event);
		$user = auth()->user();

		if (!$this->verifyConfirmationToken($user->user, $event->event, $action, $token)) {
			return response()->json([
				'success' => false,
				'message' => 'Invalid or expired token.'
			], 400);
		}

		if ($action === 'participation') {
			$event->attendees()->updateExistingPivot($user->user, ['confirmed' => true]);

			return response()->json([
				'success' => true,
				'message' => 'Participation confirmed.'
			]);
		} elseif ($action === 'cancellation') {
			$event->attendees()->detach($user->user);

			return response()->json([
				'success' => true,
				'message' => 'Cancellation confirmed.'
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Invalid action.'
		], 400);
	}
}