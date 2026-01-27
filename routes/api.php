<?php

use App\Http\Controllers\Api\EchelonApiController;
use App\Http\Controllers\Api\InstansiApiController;
use App\Http\Controllers\Api\PositionApiController;
use App\Http\Controllers\Api\RankApiController;
use App\Http\Controllers\Api\SptApiController;
use App\Http\Controllers\Api\SptMemberApiController;
use App\Http\Controllers\Api\TravelGradeApiController;
use App\Http\Controllers\Api\UnitApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| All API routes require API key authentication via ApiKeyAuth middleware.
|
*/

// Apply API key authentication to all routes
Route::middleware([ApiKeyAuth::class])->group(function () {
    // User API Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::get('/{user}', [UserApiController::class, 'show']);
    });

    // Unit API Routes
    Route::prefix('units')->group(function () {
        Route::get('/', [UnitApiController::class, 'index']);
        Route::get('/{unit}', [UnitApiController::class, 'show']);
    });

    // Instansi API Routes
    Route::prefix('instansis')->group(function () {
        Route::get('/', [InstansiApiController::class, 'index']);
        Route::get('/{instansi}', [InstansiApiController::class, 'show']);
    });

    // Position API Routes
    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionApiController::class, 'index']);
        Route::get('/{position}', [PositionApiController::class, 'show']);
    });

    // Rank API Routes
    Route::prefix('ranks')->group(function () {
        Route::get('/', [RankApiController::class, 'index']);
        Route::get('/{rank}', [RankApiController::class, 'show']);
    });

    // Travel Grade API Routes
    Route::prefix('travel-grades')->group(function () {
        Route::get('/', [TravelGradeApiController::class, 'index']);
        Route::get('/{travelGrade}', [TravelGradeApiController::class, 'show']);
    });

    // Echelon API Routes
    Route::prefix('echelons')->group(function () {
        Route::get('/', [EchelonApiController::class, 'index']);
        Route::get('/{echelon}', [EchelonApiController::class, 'show']);
    });

    // SPT API Routes
    Route::prefix('spts')->group(function () {
        Route::get('/', [SptApiController::class, 'index']);
        Route::get('/{spt}', [SptApiController::class, 'show']);
    });

    // SPT Member API Routes
    Route::prefix('spt-members')->group(function () {
        Route::get('/', [SptMemberApiController::class, 'index']);
        Route::get('/{sptMember}', [SptMemberApiController::class, 'show']);
    });
});
