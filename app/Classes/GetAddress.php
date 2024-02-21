<?php

namespace App\Classes;

use App\traits\Jsonify;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;

class GetAddress
{
    use Jsonify;

    protected string $BaseUrl =  "https://api.getAddress.io";

    protected string $api_key;

    protected Http|PendingRequest $HttpClient;



    public function __construct()
    {
        $this->api_key = config('credentials.GET_ADDRESS_API_KEY', null);
        $this->HttpClient =  Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(120)->retry(3, 1000);
    }

    public function getCompleteAddress(string $address, ?string $postCode, string $funcitonName = 'autocomplete'): ?string
    {
        try {
            $response = $this->HttpClient->post("{$this->BaseUrl}/{$funcitonName}/{$address}?api-key={$this->api_key}", [
                'filter' => [
                    'postcode' => $postCode,
                ],
                "top" => 1,
                "template" => "{formatted_address} -- {country}"
            ]);
            return $response->successful() ? data_get($response->json(), 'suggestions.0.address', null) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
