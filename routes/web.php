<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::post('chat', ChatController::class)
    ->withoutMiddleware(VerifyCsrfToken::class);