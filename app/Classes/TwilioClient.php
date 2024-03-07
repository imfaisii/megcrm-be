<?php

use Aloha\Twilio\Twilio;

class TwilioService
{
    protected $client;

    public function __construct(protected string $name)
    {
        $accountSid = config('services.twilio.sid');
        $authToken = env('services.twilio.token');

        $this->client = new Twilio($accountSid, $authToken, $name);
    }

    public function message(string $to, string $message)
    {
        return $this->client->message($to, $message);
    }
}
