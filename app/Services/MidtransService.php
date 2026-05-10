<?php

namespace App\Services;

use Midtrans\Config;

class MidtransService
{
    public static function init(): void
    {
        Config::$serverKey = config('midtrans.server_key');

        Config::$isProduction = config('midtrans.is_production');

        Config::$isSanitized = config('midtrans.is_sanitized');

        Config::$is3ds = config('midtrans.is_3ds');
    }
}
