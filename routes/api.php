<?php

use App\Http\Controllers\Api\DeploymentCheckController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::post('{project}/checks', [ProjectController::class, 'storeCheck']);
    Route::get('{project}/readiness', [ProjectController::class, 'readiness']);
});

Route::patch('checks/{check}/complete', [DeploymentCheckController::class, 'complete']);
