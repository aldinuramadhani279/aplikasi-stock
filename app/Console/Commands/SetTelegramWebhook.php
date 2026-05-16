<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Daftarkan URL webhook ke Telegram Bot API';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Mendaftarkan webhook ke Telegram...');
        $result = $telegram->setWebhook();

        if ($result) {
            $this->info('✅ Webhook berhasil didaftarkan.');
            return Command::SUCCESS;
        }

        $this->error('❌ Gagal mendaftarkan webhook. Pastikan Bot Token dan URL sudah benar.');
        return Command::FAILURE;
    }
}
