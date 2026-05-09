<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BiteshipService
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.biteship.base_url');

        $this->apiKey = config('services.biteship.api_key');
    }

    /**
     * Mendapatkan ongkir realtime
     */
    public function getRates(
        string $destinationPostalCode,
        array $items
    ) {
        $response = Http::withHeaders([

            'Authorization' => $this->apiKey,

            'Content-Type' => 'application/json',

        ])->post(
            $this->baseUrl . '/rates/couriers',
            [

                'origin_postal_code' => config('services.store.postal_code'),

                'destination_postal_code' => $destinationPostalCode,

                'couriers' => $couriers,

                'items' => $items,
            ]
        );

        return $response->json();
    }
}
