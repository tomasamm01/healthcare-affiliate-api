<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Healthcare Affiliate API',
        'version' => '1.0.0',
        'description' => 'Professional REST API for healthcare affiliate management',
    ]);
});
