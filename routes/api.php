<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;



Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('validateToken', [LoginController::class, 'validateToken'])->name('validateToken');

Route::middleware(['jwt.auth', 'logs'])->group(function () {

    //User
    Route::prefix('user')->group(function () {
        Route::get('search', [UserController::class, 'paginate']);
        Route::post('/', [UserController::class, 'create']);
        Route::get('info', [UserController::class, 'userInfo']);
        Route::get('{id}', [UserController::class, 'getById']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'delete']);
    });
});
