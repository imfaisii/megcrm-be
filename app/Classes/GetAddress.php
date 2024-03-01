<?php

namespace App\Classes;

use App\traits\Jsonify;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetAddress
{
    use Jsonify;

    public function getSuggestions(string $postCode)
    {
        $token = config('app.get_address_api');

        try {
            $postCode = Str::upper(str_replace(" ", "", preg_replace('/[^a-zA-Z0-9\s]/', '', $postCode)));

            $request = Http::withHeaders([
                'X-Api-Key' => $token
            ])
                ->get("https://api.addressian.co.uk/v2/autocomplete/$postCode");

            $postCodeRequest = Http::withHeaders([
                'X-Api-Key' => $token
            ])
                ->get("https://api.addressian.co.uk/v1/postcode/$postCode");

            if ($request->successful()) {
                $postCodeResponseCountry = Arr::get($postCodeRequest->json(), 'country', null);
                $addresses = $request->json();
                $result = [];

                foreach ($addresses as $key => $address) {
                    $transformedAddress = implode(" ", $address['address']);

                    if (isset($address['city'])) {
                        $transformedAddress .= ', ' . $address['city'];
                    }

                    if (isset($address['county'])) {
                        $transformedAddress .= ', ' . $address['county'];
                    }

                    if ($postCodeResponseCountry) {
                        $transformedAddress .= " -- $postCodeResponseCountry";
                    }

                    $result[] = [
                        'address' => $transformedAddress,
                        'post_code' => str_replace(" ", "", $address['postcode']),
                        'plain_address' => implode(" ", $address['address']),
                        'city' => $address['city'] ?? null,
                        'county' => $address['county'] ?? null,
                        'country' => $postCodeResponseCountry ?? null,
                    ];
                }

                return $result;
            } else {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }
    }

    public function adressionApi(string $postCode, string $query)
    {
        $token = config('app.get_address_api');

        try {
            $postCode = Str::upper(str_replace(" ", "", preg_replace('/[^a-zA-Z0-9\s]/', '', $postCode)));
            $query = preg_replace('/[^a-zA-Z0-9\s]/', '', $query);

            $request = Http::withHeaders([
                'X-Api-Key' => $token
            ])
                ->get("https://api.addressian.co.uk/v2/autocomplete/$postCode $query");

            $postCodeRequest = Http::withHeaders([
                'X-Api-Key' => $token
            ])
                ->get("https://api.addressian.co.uk/v1/postcode/$postCode");

            if ($request->successful()) {
                $postCodeResponseCountry = Arr::get($postCodeRequest->json(), 'country', null);
                $address = $request->json()[0];
                $transformedAddress = implode(" ", $address['address']);

                if (isset($address['city'])) {
                    $transformedAddress .= ', ' . $address['city'];
                }

                if (isset($address['county'])) {
                    $transformedAddress .= ', ' . $address['county'];
                }

                if ($postCodeResponseCountry) {
                    $transformedAddress .= " -- $postCodeResponseCountry";
                }

                return [
                    str_replace(" ", "", $address['postcode']),
                    $transformedAddress,
                    implode(" ", $address['address']),
                    $address['city'] ?? null,
                    $address['county'] ?? null,
                    $postCodeResponseCountry ?? null
                ];
            } else {
                Log::channel('addresso_api')
                    ->info("Error in postcode:: $postCode and address:: $query");

                return [$postCode, $query, null, null, null, null];
            }
        } catch (Exception $e) {
            Log::channel('addresso_api')
                ->info("Exception in postcode:: $postCode and address:: $query {$e->getMessage()}");

            return [$postCode, $query, $query, null, null, null];
        }
    }
}
