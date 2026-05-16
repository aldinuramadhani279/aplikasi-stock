<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Telegram Webhook Route
|--------------------------------------------------------------------------
| Route ini menerima POST request dari server Telegram.
| Tidak perlu CSRF karena webhook dari Telegram.
*/

Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');
