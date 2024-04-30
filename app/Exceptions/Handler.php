<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (!app()->environment('local')) {
                Log::driver('slack_exceptions')->error(json_encode([
    'message' => $e->getMessage(),
    'file' => $e->getFile(),
    'line' => $e->getLine(),
    'host' => request()->getHttpHost(),
    'ip' => request()->ip(),
    'url' => request()->fullUrl(),
    'method' => request()->method(),
    'user_id' => auth()->check() ? auth()->id() : null,
    'headers' => request()->headers->all(),
    'payload' => request()->all(),
    'stack_trace' => $e->getTraceAsString(),
]));
            }
        });
    }
}
