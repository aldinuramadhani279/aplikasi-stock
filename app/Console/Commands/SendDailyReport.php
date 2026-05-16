<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SendDailyReport extends Command
{
    protected $signature = 'telegram:send-report';
    protected $description = 'Kirim laporan stok harian ke Telegram';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Mengirim laporan harian ke Telegram...');
        $telegram->sendDailyReport();
        $this->info('✅ Laporan berhasil dikirim.');
        return Command::SUCCESS;
    }
}
