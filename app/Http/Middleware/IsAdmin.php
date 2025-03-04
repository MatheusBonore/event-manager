<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		// Verifica se o usuário está autenticado e se o campo 'role' é igual a 'admin'
		if (Auth::check() && Auth::user()->role === 'admin') {
			return $next($request);
		}

		return response()->json([
			'success' => false,
			'message' => 'You do not have admin access'
		], 403);
	}
}
