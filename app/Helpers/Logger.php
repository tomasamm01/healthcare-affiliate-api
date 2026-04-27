<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Logger
{
    public static function affiliate(string $action, array $context = []): void
    {
        Log::channel('affiliate')->info("Affiliate: {$action}", $context);
    }

    public static function auth(string $action, array $context = []): void
    {
        Log::channel('auth')->info("Auth: {$action}", $context);
    }

    public static function coverage(string $action, array $context = []): void
    {
        Log::channel('coverage')->info("Coverage: {$action}", $context);
    }

    public static function error(string $channel, string $message, array $context = []): void
    {
        Log::channel($channel)->error($message, $context);
    }

    public static function warning(string $channel, string $message, array $context = []): void
    {
        Log::channel($channel)->warning($message, $context);
    }
}
