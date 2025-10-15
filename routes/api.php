<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\TranslationController;
use App\Http\Controllers\V1\AuthenticationController;
use App\Http\Controllers\V1\TagController;
use App\Http\Controllers\V1\LanguageController;

Route::group(['prefix' => 'v1'], function () {
  
    Route::post('/oauth/token', [AuthenticationController::class, 'login']);
    
    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('translations/search', [TranslationController::class, 'searchTranslations']);
        Route::get('translations/export', [TranslationController::class, 'export']);
        Route::apiResource('translations', TranslationController::class);
        
        // Tag routes
        Route::get('tags', [TagController::class, 'index']);
        
        // Language routes
        Route::get('languages', [LanguageController::class, 'index']);
    });
});
