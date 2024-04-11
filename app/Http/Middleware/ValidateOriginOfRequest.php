<?php

namespace App\Http\Middleware;

use Closure;
use DomainException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class ValidateOriginOfRequest
{
    private bool $validationStatus = true;

    /**
     * Handle an incoming request.
     * This is responsible for validating the incoming request if it is coming from the authorized domains
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isProduction() && (str()->contains($request->header('User-Agent'), 'Postman') || str()->contains($request->header('origin'), 'localhost'))) {

            abort(403);
        }
        $this->validationStatus = $this->CheckDomain($request);

        throw_unless($this->validationStatus, new DomainException('Sorry, This Action is Not Allowed'));

        return $next($request);
    }

    private function getOriginOfRequest(Request $request)
    {
        return $request->headers->get('origin') ?? $request->headers->get('referer', '');
    }

    private function failed(Request $request): void
    {
        //This method is responsible for logging the failed request to this domains
        try {
            Log::info(json_encode([
                'queryParams' => $request?->route()?->parameters,
                'url' => $request->url(),
                'inputParams' => $request->all(),
                'userIP' => $request->ip(),
                'userAgent' => $request->userAgent(),
                'requestHeaders' => $request->header(),
            ]));
        } catch (Exception $e) {
        }
    }

    public function CheckDomain(Request $request): bool
    {
        //This method is responsible for checking if the request is coming from the authorized domains
        try {
            if (app()->isLocal()) {
                return true;
            }
            $userDomain = ($request->headers->get('origin') ?? $request->headers->get('referer'));
            $checkResult = in_array(parse_url($userDomain, PHP_URL_HOST), config('app.allowed_domains'));
            if (! $checkResult) {
                //Log that failed attempt
                $this->failed($request);
            }

            return $checkResult;
        } catch (Exception $e) {
            return false;
        }
    }
}
