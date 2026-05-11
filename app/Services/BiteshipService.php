<?php

namespace App\Services;

use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $cacheDuration = 60; // 1 jam

    public function __construct()
    {
        $this->baseUrl = config('services.biteship.base_url');
        $this->apiKey = config('services.biteship.api_key');
    }

    /**
     * Get shipping rates menggunakan coordinates (latitude/longitude)
     * Format response siap untuk display di checkout
     *
     * @param float $originLat
     * @param float $originLng
     * @param float $destLat
     * @param float $destLng
     * @param int $weight (gram)
     * @param array $items
     * @return array
     */
    public function getRates(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng,
        int $weight = 0,
        array $items = []
    ): array {
        try {
            // Generate cache key
            $cacheKey = $this->generateCacheKey('rates', [
                'origin' => "{$originLat},{$originLng}",
                'dest' => "{$destLat},{$destLng}",
                'weight' => $weight,
            ]);

            // Return dari cache jika ada
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Prepare items untuk request
            $requestItems = empty($items) ? [
                [
                    'name' => 'Item',
                    'value' => 0,
                    'weight' => $weight,
                    'quantity' => 1,
                ]
            ] : $items;

            // Hit Biteship API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/rates/couriers', [

                'origin_latitude' => $originLat,
                'origin_longitude' => $originLng,

                'destination_latitude' => $destLat,
                'destination_longitude' => $destLng,

                'couriers' => ShippingMethod::query()

                    ->where('provider', 'biteship')

                    ->where('status', 'available')

                    ->pluck('courier_code')

                    ->implode(','),

                'items' => $requestItems,
            ]);

            if (!$response->successful()) {
                Log::warning('Biteship API error', [
                    'status' => $response->status(),
                    'error' => $response->json('error_message') ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'error' => $response->json('error_message') ?? 'Failed to fetch rates',
                    'pricing' => [],
                ];
            }

            $responseData = $response->json();

            // Format rates
            $formattedRates = $this->formatRates($responseData['pricing'] ?? []);

            $result = [
                'success' => true,
                'pricing' => $formattedRates,
                'timestamp' => now()->toDateTimeString(),
            ];

            // Cache result
            Cache::put($cacheKey, $result, $this->cacheDuration);

            return $result;
        } catch (\Exception $e) {
            Log::error('BiteshipService::getRates error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'pricing' => [],
            ];
        }
    }

    /**
     * Create shipment order di Biteship (call setelah order dibayar)
     *
     * @param array $params
     * @return array
     */
    public function createOrder(array $params): array
    {
        try {
            $this->validateOrderParams($params);

            $payload = [
                'origin_contact_name' => $params['origin_contact_name'],
                'origin_address' => $params['origin_address'],
                'origin_latitude' => $params['origin_latitude'],
                'origin_longitude' => $params['origin_longitude'],
                'destination_contact_name' => $params['destination_contact_name'],
                'destination_contact_phone' => $params['destination_contact_phone'],
                'destination_address' => $params['destination_address'],
                'destination_latitude' => $params['destination_latitude'],
                'destination_longitude' => $params['destination_longitude'],
                'courier_company' => $params['courier_company'],
                'courier_type' => $params['courier_type'],
                'items' => $params['items'],
            ];

            // Add optional fields
            if (!empty($params['origin_notes'])) {
                $payload['origin_notes'] = $params['origin_notes'];
            }
            if (!empty($params['destination_notes'])) {
                $payload['destination_notes'] = $params['destination_notes'];
            }
            if (!empty($params['destination_contact_email'])) {
                $payload['destination_contact_email'] = $params['destination_contact_email'];
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orders', $payload);

            if (!$response->successful()) {
                Log::warning('Biteship create order error', [
                    'status' => $response->status(),
                    'error' => $response->json('error_message') ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'error' => $response->json('error_message') ?? 'Failed to create order',
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'order_id' => $data['id'] ?? null,
                'tracking_id' => $data['courier_tracking_id'] ?? null,
                'status' => $data['status'] ?? null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('BiteshipService::createOrder error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get order tracking dari Biteship
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderTracking(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get($this->baseUrl . '/orders/' . $orderId);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Order not found',
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'status' => $data['status'] ?? null,
                'tracking_id' => $data['courier_tracking_id'] ?? null,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('BiteshipService::getOrderTracking error', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format rates response dari Biteship untuk display
     *
     * @param array $rates
     * @return array
     */
    protected function formatRates(array $rates): array
    {
        return array_map(function ($rate) {

            return [

                'id' => $rate['id'] ?? null,

                // Courier
                'courier_company' => $rate['courier_name'] ?? '',
                'courier_code' => $rate['courier_code'] ?? '',

                // Service
                'courier_type' => $rate['courier_service_name'] ?? '',
                'service_code' => $rate['courier_service_code'] ?? '',

                // Info
                'service_type' => $rate['service_type'] ?? '',
                'description' => $rate['description'] ?? '',

                // Harga
                'price' => (int) ($rate['price'] ?? 0),

                // Estimasi
                'etd' => $rate['duration'] ?? '—',

                // Additional
                'shipping_type' => $rate['shipping_type'] ?? '',
                'features' => $rate['features'] ?? [],

                // Raw response
                'raw' => $rate,
            ];
        }, $rates);
    }

    /**
     * Validate order parameters
     *
     * @param array $params
     * @throws \Exception
     */
    protected function validateOrderParams(array $params): void
    {
        $required = [
            'origin_contact_name',
            'origin_address',
            'origin_latitude',
            'origin_longitude',
            'destination_contact_name',
            'destination_contact_phone',
            'destination_address',
            'destination_latitude',
            'destination_longitude',
            'courier_company',
            'courier_type',
            'items',
        ];

        foreach ($required as $key) {
            if (!isset($params[$key]) || ($params[$key] === '' && $key !== 'destination_notes')) {
                throw new \Exception("Missing required parameter: {$key}");
            }
        }
    }

    /**
     * Generate cache key
     *
     * @param string $prefix
     * @param array $data
     * @return string
     */
    protected function generateCacheKey(string $prefix, array $data): string
    {
        return $prefix . '_' . md5(json_encode($data));
    }

    /**
     * Clear cache untuk coordinates tertentu
     *
     * @param float $originLat
     * @param float $originLng
     * @param float $destLat
     * @param float $destLng
     * @return void
     */
    public function clearRatesCache(
        float $originLat,
        float $originLng,
        float $destLat,
        float $destLng
    ): void {
        $cacheKey = $this->generateCacheKey('rates', [
            'origin' => "{$originLat},{$originLng}",
            'dest' => "{$destLat},{$destLng}",
        ]);

        Cache::forget($cacheKey);
    }
}
