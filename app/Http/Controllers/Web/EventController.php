<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\StoreEventRequest;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
	public function index(Request $request): View
	{
		$creator = $request->query('creator');
		$attendees = $request->query('attendees');

		$events = Event::with(['creator', 'attendees'])->when($creator, function ($query) use ($creator) {
			return $query->where('users_user', $creator);
		})->when($attendees, function ($query) use ($attendees) {
			return $query->whereHas('attendees', function ($query) use ($attendees) {
				return $query->where('users_user', $attendees);
			});
		})->get()->map(function ($event) {
			// Gerar as iniciais dos nomes
			$parts = explode(" ", $event->creator->name);

			$initials = "";
			foreach ($parts as $part) {
				$initials .= strtoupper($part[0]);
			}

			$event->creator->initials_name = $initials;

			$event->attendees->transform(function ($user) {
				$parts = explode(" ", $user->name);

				$initials = "";
				foreach ($parts as $part) {
					$initials .= strtoupper($part[0]);
				}

				$user->initials_name = $initials;
				return $user;
			});

			// Gerar a descrição truncada
			$length = 30;

			// Encontrar a última ocorrência do espaço dentro do limite de tamanho
			$pos = strrpos(substr($event->description . ' ', 0, $length), ' ');

			$event->truncated_description = strlen($event->description) > $length
				? substr($event->description, 0, $pos - 1) . '...'
				: $event->description;


			// Formatar data de inicio do evento
			$event->start_date_formatted = Carbon::parse($event->start_time)->format('l, d F Y H:i');

			// Calcular as datas do evento
			$startDate = Carbon::parse($event->start_time);
			$endDate = Carbon::parse($event->end_time);

			if ($startDate->isPast()) {
				// Evento já passou
				$event->event_status = 'Evento já passou';
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

			return $event;
		});

		$parts = explode(" ", Auth::user()->name);

		$initials = "";
		foreach ($parts as $part) {
			$initials .= strtoupper($part[0]);
		}

		$initials_name = $initials;

		$user = [
			'user' => Auth::user()->user,
			'name' => Auth::user()->name,
			'initials_name' => $initials_name,
			'email' => Auth::user()->email
		];

		return view('event.events', [
			'user' => $user,
			'events' => $events
		]);
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
}