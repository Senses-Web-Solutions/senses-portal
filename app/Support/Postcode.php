<?php

namespace App\Support;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Postcode
{
    public static function getCoordinates($postcode)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->get('api.postcodes.io/postcodes/' . $postcode);

            if ($response->status() == 200) {
                return [
                    'latitude' => $response->json()['result']['latitude'],
                    'longitude' => $response->json()['result']['longitude']
                ];
            }
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        } catch (Exception $err) {
            // fail silently
            Log::info('Fail: ' . $err->getMessage());
            return [
                'latitude' => null,
                'longitude' => null,
            ];
        }
    }
}
