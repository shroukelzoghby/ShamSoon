<?php

use App\Http\Controllers\API\v1\Auth\UserAuthController;
use App\Http\Controllers\API\v1\Auth\UserRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('users')
    ->name('users.')
    ->group(function (){
        Route::get('/register',UserRegisterController::class)
            ->name('register');
        Route::get('/login',[UserAuthController::class,'authenticate'])
            ->name('login');
});
