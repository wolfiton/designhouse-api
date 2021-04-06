<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\SettingsController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Public routes
Route::get('me', [MeController::class, 'getMe']);


// Routes for auth users
Route::group(['middleware'=>'auth:api'], function(){

    Route::post('logout', [LoginController::class, 'logout']);
    Route::put('settings/profile', [SettingsController::class, 'updateProfile']);
    Route::put('settings/password', [SettingsController::class, 'updatePassword']);
});


// Routes for guests



    
    Route::group(['middleware'=>'guest:api'], function(){

        Route::post('/register', [RegisterController::class, 'register']);
        Route::post('/verification/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('/verification/resend', [VerificationController::class, 'resend'])->name('verification.resend');
        Route::post('login', [LoginController::class, 'login']);
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset']);
    });
    
    
    
