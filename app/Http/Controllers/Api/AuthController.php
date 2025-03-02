<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	public function register(RegisterRequest $request): JsonResponse
	{
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
		]);

		return response()->json([
			'user' => $user,
			'token' => $user->createToken(config('app.name'))->plainTextToken,
		], 201);
	}

	public function login(LoginRequest $request): JsonResponse
	{
		if (!Auth::attempt($request->validated())) {
			return response()->json([
				'message' => 'Invalid credentials'
			], 401);
		}

		$user = Auth::user();

		return response()->json([
			'user' => $user,
			'token' => $user->createToken(config('app.name'))->plainTextToken,
		]);
	}

	public function logout(Request $request): JsonResponse
	{
		$request->user()->tokens()->delete();

		return response()->json(['message' => 'Logout successful']);
	}
}
