<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\WebHookController;

Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function () {

    // Auth Routes
    Route::get('/user', [UserController::class, 'getUserAuthenticated'])->middleware('auth:sanctum');
    Route::post('/login', [UserController::class, 'login'])->middleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ]);
    Route::post('/register', [UserController::class, 'register']);


    // Games Route
    Route::prefix('games')->group(function () {
        Route::get('/details/{id}', [GameController::class, 'details']);
        Route::get('/all/{pagination?}', [GameController::class, 'all']);
        Route::get('/filter', [GameController::class, 'filterGame']);
        Route::get('/most-bought', [GameController::class, 'gameMostBought']);
    });

    // Profile routes
    Route::prefix('profile')->middleware(['auth:sanctum', 'refresh.token', 'abilities:view-profile'])->group(function () {
        Route::get('/show', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'updateProfile']);
    });

    // Shopping Cart Routes
    Route::prefix('cart')->middleware(['auth:sanctum', 'refresh.token', 'abilities:cart'])->group(function () {
        Route::get('show', [ShoppingCartController::class, 'show'])->name('showGame');
        Route::post('/create', [ShoppingCartController::class, 'create']);
        Route::get('/cart/cancel', [ShoppingCartController::class, 'cancel']);
    });

    // Library Routes
    Route::prefix('library')->middleware(['auth:sanctum', 'refresh.token', 'abilities:library'])->group(function () {
        Route::get('/games', [LibraryController::class, 'show']);
    });

    // Payment Routes
    Route::post('/buy', [PaymentController::class, 'buy'])->middleware(['auth:sanctum', 'refresh.token', 'abilities:buy']);
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment'])->middleware(['auth:sanctum', 'refresh.token']);

    Route::post('/stripe/webhook', [WebHookController::class, 'handleWebhook']);
});
