<?php

namespace App\Http\Controllers\Web;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
	public function index(): View
	{
		$events = Event::with(['user', 'users'])->get()->map(function ($event) {
			// Gerar as iniciais dos nomes

			$parts = explode(" ", $event->user->name);

			$initials = "";
			foreach ($parts as $part) {
				$initials .= strtoupper($part[0]);
			}
			
			$event->user->initials_name = $initials;
			
			$event->users->transform(function ($user) {
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

			return $event;
		});

		return view('dashboard.events', [
			'events' => $events
		]);
	}
}