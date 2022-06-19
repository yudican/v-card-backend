<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactInfoController;
use App\Http\Controllers\Api\SocialLinkController;
use App\Http\Controllers\Api\SocialNetworkController;
use App\Http\Controllers\Api\UserController;
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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('user.login');
});
Route::prefix('master')->middleware(['auth:sanctum'])->group(function () {
    // get api resource controller
    Route::apiResource('contact-info', ContactInfoController::class);
    Route::apiResource('social-link', SocialLinkController::class);
    Route::apiResource('social-network', SocialNetworkController::class);
    Route::post('user/update-card', [UserController::class, 'update']);
    Route::get('user/profile', [UserController::class, 'getUserProfile']);
});
