<?php

namespace App\Http\Controllers\OTP;

use App\Cache\TwoFactorCodeCacheClass;
use App\Events\LoginUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\OTP\OTPVerificationRequest;
use App\traits\Jsonify;
use Exception;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    use Jsonify;
    public function check(OTPVerificationRequest $request)
    {
        try {
            if (RateLimiter::tooManyAttempts('OTP-MESSAGE-CHECK:' . $request->user()->id, $perMinute = 5)) {
                return $this->error("Too many attempts. please try again after " . RateLimiter::availableIn('OTP-MESSAGE-CHECK:' . $request->user()->id));
            }

            $result = RateLimiter::attempt(
                'OTP-MESSAGE-CHECK:' . $request->user()->id,
                $perMinute = 5,
                function () use ($request) {
                    $cacheObj = new TwoFactorCodeCacheClass();
                    return strtolower($request->get('OTP_Code', 0)) === strtolower($cacheObj->getData($request?->user()?->id));
                },
                $decayRate = 60,
            );
            if ($result) {
                $request->user()->update([
                    'last_otp' => $request->get('OTP_Code')
                ]);
                return $this->success("OTP verified");
            } else {
                return $this->error("OTP not verified");
            }
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }

    public function generate(Request $request)
    {
        try {
            if (RateLimiter::tooManyAttempts('OTP-MESSAGE-GENERATE:' . $request->user()->id, $perMinute = 1)) {
                return $this->error("Too many attempts. please try again after " . RateLimiter::availableIn('OTP-MESSAGE-GENERATE:' . $request->user()->id));
            }

            $result = RateLimiter::attempt(
                'OTP-MESSAGE-GENERATE:' . $request->user()->id,
                $perMinute = 1,
                function () use ($request) {
                    return LoginUserEvent::dispatch(auth()->user());
                },
                $decayRate = 60,
            );
            if ($result) {
                return $this->success("OTP Sent Successfully", data: app()->isLocal() ? (new TwoFactorCodeCacheClass())->getData($request?->user()?->id) : null);
            } else {
                return $this->error("OTP not Sent");
            }
        } catch (Exception $e) {
            return $this->exception($e);
        }
    }
}
