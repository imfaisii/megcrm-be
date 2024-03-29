<?php

namespace App\Http\Controllers\Auth;

use App\Events\LoginUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        if (Auth::attempt($request->validated())) {
            $token = auth()->user()->createToken('auth_token', expiresAt: now()->addMinutes(config('session.lifetime')))->plainTextToken;
            LoginUserEvent::dispatch(auth()->user());
            return $this->success(data: [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $request->user(),
            ]);
        }

        return $this->error('Invalid credentials', 401);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {  
        $request->user()->currentAccessToken()->delete();
        $request->session()->invalidate();

        return $this->success();
    }
}
