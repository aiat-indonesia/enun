<?php

use App\Http\Controllers\Api\V1\GeoController;
use App\Http\Controllers\Api\V1\PlaceController;
use App\Http\Controllers\Api\V1\WorkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API endpoints - no authentication required
Route::prefix('v1')->group(function () {
    // Works endpoints
    Route::get('/works', [WorkController::class, 'index']);
    Route::get('/works/{work}', [WorkController::class, 'show']);

    // Places endpoints
    Route::get('/places', [PlaceController::class, 'index']);
    Route::get('/places/{place}', [PlaceController::class, 'show']);

    // Geo endpoints for mapping
    Route::get('/geo/points', [GeoController::class, 'points']);
});
