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

    public function adressionApi(string $postCode, string $query)
    {
        try {
            $postCode = Str::upper(str_replace(" ", "", preg_replace('/[^a-zA-Z0-9\s]/', '', $postCode)));
            $query = preg_replace('/[^a-zA-Z0-9\s]/', '', $query);

            $request = Http::withHeaders([
                'X-Api-Key' => 'KfvssHszRO5FysZPQvFmj5N1zA1rZjuU56DpmWfy'
            ])
                ->get("https://api.addressian.co.uk/v2/autocomplete/$postCode $query");

            $postCodeRequest = Http::withHeaders([
                'X-Api-Key' => 'KfvssHszRO5FysZPQvFmj5N1zA1rZjuU56DpmWfy'
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

                dump($query);
                return [$postCode, $query, null, null, null, null];
            }
        } catch (Exception $e) {
            Log::channel('addresso_api')
                ->info("Exception in postcode:: $postCode and address:: $query {$e->getMessage()}");

            dump("1", $e->getMessage());

            return [$postCode, $query, null, null, null, null];
        }
    }
}
