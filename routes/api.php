<?php

use App\Http\Controllers\API\v1\Auth\CheckOTPController;
use App\Http\Controllers\API\v1\Auth\ForgetPasswordController;
use App\Http\Controllers\API\v1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\v1\Auth\SocialiteController;
use App\Http\Controllers\API\v1\CarbonController;
use App\Http\Controllers\API\v1\Community\CommentController;
use App\Http\Controllers\API\v1\Community\PostController;
use App\Http\Controllers\API\V1\FeedbackController;
use App\Http\Controllers\API\v1\NotificationController;
use App\Http\Controllers\API\v1\SolarPanelController;
use App\Http\Controllers\API\v1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\Auth\UserAuthController;
use App\Http\Controllers\API\v1\Auth\UserRegisterController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;

Route::prefix('users')
    ->name('users.')
    ->group(function () {
        // Public routes - No Need for authentication
        Route::post('/register', UserRegisterController::class)
            ->name('register');
        Route::post('/login', [UserAuthController::class, 'authenticate'])
            ->name('login');
        Route::post('/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
        Route::post('/check-otp', [CheckOTPController::class, 'verifyOTP']);
        Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);
        Route::get('auth/google',[SocialiteController::class,'redirectToGoogle']);
        Route::get('auth/google/callback',[SocialiteController::class,'handleGoogleCallback']);


        // Private routes - Need authentication
        Route::middleware('auth:sanctum')
            ->group(function () {
            Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
            Route::post('/email/verify/send', [EmailVerificationController::class, 'sendEmailVerification'])
                ->name('email.verification.send');
            Route::post('/email/verify', [EmailVerificationController::class, 'VerifyEmail'])
                ->name('email.verify');

            // Post Routes
            Route::apiResource('posts', PostController::class);

            // Comment Routes
            Route::apiResource('posts.comments', CommentController::class)->except(['show']);

            //FeedBack
            Route::apiResource('feedbacks', FeedbackController::class)->only(['store']);

            //SolarPanel
            Route::get('solarPanels', [SolarPanelController::class, 'index'])->name('solarPanels');
            Route::get('solarPanels/{id}', [SolarPanelController::class, 'show'])->name('solarPanel');

            //Carbon
            Route::post('/carbon', [CarbonController::class, 'store'])->name('carbon.store');
            Route::get('/carbons/{id}', [CarbonController::class, 'show'])->name('carbon.show');

            Route::post('/store-fcm-token', [UserController::class, 'storeFcmToken']);
            Route::post('/notify-ai-result', [NotificationController::class, 'notifyAIResult']);



        });
    });

