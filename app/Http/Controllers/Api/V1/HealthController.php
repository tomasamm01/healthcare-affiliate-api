<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // Database connection check
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'ok',
                'connection' => config('database.default'),
            ];
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $checks['database'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        // Cache check
        try {
            $cacheKey = 'health_check_' . time();
            cache()->put($cacheKey, 'test', 10);
            $value = cache()->get($cacheKey);
            cache()->forget($cacheKey);

            if ($value === 'test') {
                $checks['cache'] = [
                    'status' => 'ok',
                    'driver' => config('cache.default'),
                ];
            } else {
                $status = 'degraded';
                $checks['cache'] = [
                    'status' => 'error',
                    'error' => 'Cache read/write failed',
                ];
            }
        } catch (\Exception $e) {
            $status = 'degraded';
            $checks['cache'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        // External service check (optional - configurable)
        $externalServiceUrl = env('HEALTH_CHECK_EXTERNAL_URL');
        if ($externalServiceUrl) {
            try {
                $response = Http::timeout(5)->get($externalServiceUrl);
                $checks['external_service'] = [
                    'status' => $response->successful() ? 'ok' : 'error',
                    'url' => $externalServiceUrl,
                    'status_code' => $response->status(),
                ];
                if (!$response->successful()) {
                    $status = 'degraded';
                }
            } catch (\Exception $e) {
                $status = 'degraded';
                $checks['external_service'] = [
                    'status' => 'error',
                    'url' => $externalServiceUrl,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $statusCode = $status === 'healthy' ? 200 : ($status === 'degraded' ? 200 : 503);

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $statusCode);
    }
}
