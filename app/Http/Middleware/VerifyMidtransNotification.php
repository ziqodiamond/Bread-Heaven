<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMidtransNotification
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip verification in local development jika diperlukan
        if (config('app.env') === 'local' && $request->has('skip_verification')) {
            return $next($request);
        }

        $data = $request->all();
        $signature = $request->header('X-Signature-Key') ?? $request->input('signature_key');

        if (!$signature) {
            return response()->json([
                'success' => false,
                'error' => 'Missing signature',
            ], 401);
        }

        // Verify signature
        $midtransService = app(\App\Services\MidtransService::class);
        $isValid = $midtransService->verifyNotificationSignature($data, $signature);

        if (!$isValid) {
            \Log::warning('Invalid Midtrans signature', [
                'data' => $data,
                'provided_signature' => $signature,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid signature',
            ], 401);
        }

        return $next($request);
    }
}
