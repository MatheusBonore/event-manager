<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait GeneratesConfirmationToken
{
	public function generateConfirmationToken($user, $event, $action)
	{
		$token = Str::random(32);
		$cacheKey = "event_confirmation:{$action}:{$user}:{$event}";
	
		$tokens = Cache::get($cacheKey, []);
		$tokens[] = $token;
	
		Cache::put($cacheKey, $tokens, now()->addMinutes(60));
	
		return url("/events/{$event}/confirm/{$action}/{$token}");
	}

	public function verifyConfirmationToken($user, $event, $action, $token)
	{
		$cacheKey = "event_confirmation:{$action}:{$user}:{$event}";
	
		$tokens = Cache::get($cacheKey, []);
	
		if (in_array($token, $tokens)) {
			$tokens = array_diff($tokens, [$token]);
			Cache::put($cacheKey, $tokens, now()->addMinutes(60)); // Atualiza o cache com a lista sem o token usado
	
			return true;
		}
	
		return false;
	}
}
