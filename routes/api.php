<?php

use App\Http\Controllers\API\v1\Auth\UserAuthController;
use App\Http\Controllers\API\v1\Auth\UserRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users.')
    ->group(function (){
        // Public routes - No Need for authentication
        Route::post('/register',UserRegisterController::class)
            ->name('register');
        Route::post('/login',[UserAuthController::class,'authenticate'])
            ->name('login');

        // Private routes - Need authentication
        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::post('/logout',[UserAuthController::class,'logout'])->name('logout');
            });
});

