<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::post('chat',[ChatController::class, 'chat'])
    ->withoutMiddleware(VerifyCsrfToken::class);