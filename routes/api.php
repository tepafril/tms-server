<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\TypeController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        'tickets' => TicketController::class,
        'types' => TypeController::class,
        'levels' => LevelController::class,
        'users' => UserController::class
    ]);

    Route::get('/roles', [UserController::class, 'roles']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/tickets/resolve/{id}', [TicketController::class, 'resolve']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);