<?php

/**
 * Telegram webhook
 */

use App\Http\Controllers\TelegramWebhookController;

Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');
