<?php

namespace App\Http\Controllers\Web;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
	public function index(): View
	{
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

		$created_events = Event::where('users_user', Auth::id())->count();

		$open_events = Event::where('status', 'open')->count();
		$closed_events = Event::where('status', 'closed')->count();
		$canceled_events = Event::where('status', 'canceled')->count();

		$attending_events = Event::whereHas('attendees', function ($query) {
			$query->where('users_user', Auth::user()->user);
		})->count();

		return view('dashboard', [
			'user' => $user,
			'created_events' => $created_events,
			'open_events' => $open_events,
			'closed_events'=> $closed_events,
			'canceled_events'=> $canceled_events,
			'attending_events' => $attending_events
		]);
	}
}