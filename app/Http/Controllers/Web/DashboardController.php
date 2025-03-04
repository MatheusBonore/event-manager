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

		return view('dashboard', [
			'user' => $user,
			'created_events' => $created_events,
		]);
	}
}