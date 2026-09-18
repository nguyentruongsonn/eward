<?php

use App\Http\Controllers\AdminMailController;
use App\Http\Controllers\ApiOnlyFallbackController;
use App\Http\Middleware\RequestIdMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/email-reply', [AdminMailController::class, 'receiveMailReply'])
    ->name('webhook.email-reply');

Route::fallback(ApiOnlyFallbackController::class)
    ->middleware(RequestIdMiddleware::class);
