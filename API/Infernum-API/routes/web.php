<?php

use App\Http\Controllers\UserController;

Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers'], function () {

    Route::get('/dashboard', function ()
    {
        return view('welcome');
    })->middleware(['auth:sanctum' ,'refresh.token', 'abilities:admin']);


});

Route::get('/administratorPanel/autologin', [UserController::class, 'autologin'])->name('admin.autologin');

Route::get('/login', function () {
    $host = request()->getHost();
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return redirect('http://localhost:4220/login');
    }
    $frontendUrl = env('FRONTEND_URL', 'https://frontend-infernum-original.duckdns.org');
    return redirect(rtrim($frontendUrl, '/') . '/login');
})->name('login');

Route::get('/storage/{path}', function ($path) {
    if (str_contains($path, '..')) {
        abort(400, 'Invalid path.');
    }
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');
