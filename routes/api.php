<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('company', CompanyController::class);
    Route::apiResource('team', TeamController::class);
});

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', 'fetchUser');
        Route::get('logout', 'logout');
        Route::post('confirm_password', 'confirmPassword');
    });
});
