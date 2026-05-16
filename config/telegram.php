<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    | Token dari @BotFather di Telegram. Wajib diisi di .env
    | TELEGRAM_BOT_TOKEN=your_bot_token_here
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    | URL publik yang akan menerima update dari Telegram.
    | Harus HTTPS. Set di .env:
    | TELEGRAM_WEBHOOK_URL=https://yourdomain.com/telegram/webhook
    */
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Chat ID
    |--------------------------------------------------------------------------
    | Chat ID default untuk notifikasi umum.
    | TELEGRAM_DEFAULT_CHAT_ID=123456789
    */
    'default_chat_id' => env('TELEGRAM_DEFAULT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Report Chat ID
    |--------------------------------------------------------------------------
    | Chat ID khusus untuk laporan harian otomatis.
    | TELEGRAM_REPORT_CHAT_ID=123456789
    */
    'report_chat_id' => env('TELEGRAM_REPORT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Alert Chat ID
    |--------------------------------------------------------------------------
    | Chat ID khusus untuk notifikasi stok menipis/habis.
    | TELEGRAM_ALERT_CHAT_ID=123456789
    */
    'alert_chat_id' => env('TELEGRAM_ALERT_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Async Request
    |--------------------------------------------------------------------------
    | Kirim request ke Telegram secara async menggunakan Queue
    */
    'async_requests' => env('TELEGRAM_ASYNC', true),

    /*
    |--------------------------------------------------------------------------
    | Parse Mode Default
    |--------------------------------------------------------------------------
    | Mode parsing teks: HTML atau Markdown
    */
    'parse_mode' => 'HTML',
];
