<?php

use App\Http\Controllers\Api\V1\AffiliateController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CoverageController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\PlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Health check (public)
    Route::get('health', [HealthController::class, 'check']);

    // Authentication (public)
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::post('auth/login', [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Affiliates - Admin/Operator only for write operations
        Route::apiResource('affiliates', AffiliateController::class)->except(['index', 'show']);
        Route::middleware('role:admin,operator,viewer')->group(function () {
            Route::get('affiliates', [AffiliateController::class, 'index']);
            Route::get('affiliates/{affiliate}', [AffiliateController::class, 'show']);
        });
        
        Route::middleware('role:admin,operator')->group(function () {
            Route::post('affiliates/{affiliate}/status', [AffiliateController::class, 'changeStatus']);
            Route::post('affiliates/{affiliate}/dependents', [AffiliateController::class, 'addDependent']);
            Route::delete('affiliates/{affiliate}/dependents/{dependent}', [AffiliateController::class, 'removeDependent']);
        });

        Route::get('affiliates/{affiliate}/status', [AffiliateController::class, 'getStatus']);
        Route::get('affiliates/{affiliate}/family-group', [AffiliateController::class, 'getFamilyGroup']);

        // Plans - Admin only for write operations
        Route::apiResource('plans', PlanController::class)->except(['index', 'show']);
        Route::middleware('role:admin,operator,viewer')->group(function () {
            Route::get('plans', [PlanController::class, 'index']);
            Route::get('plans/{plan}', [PlanController::class, 'show']);
        });

        // Coverage - All authenticated users
        Route::post('coverage/validate', [CoverageController::class, 'validate']);
        Route::get('coverage/affiliate/{affiliateId}', [CoverageController::class, 'getAffiliateCoverage']);
    });
});
