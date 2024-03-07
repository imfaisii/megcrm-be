<?php

namespace App\Services;

use Aloha\Twilio\Twilio;
use Exception;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;

    public function __construct(protected string $name)
    {
        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.token');

        $this->client = new Twilio($accountSid, $authToken, $name);
    }

    public function message(string $to, string $message)
    {
        try {
            $this->client->message($to, $message);
        } catch (Exception $e) {
            Log::channel('twilio')->error("Failed to send sms to: $to, exception: " . $e->getMessage());

            throw new Exception($e->getMessage());
        }
    }
}
