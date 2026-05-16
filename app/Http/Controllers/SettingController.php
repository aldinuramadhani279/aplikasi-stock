<?php

namespace App\Http\Controllers;

use App\Models\TelegramSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(protected TelegramService $telegram)
    {
    }

    public function telegram()
    {
        $settings = TelegramSetting::pluck('value', 'key')->toArray();
        return view('settings.telegram', compact('settings'));
    }

    public function telegramUpdate(Request $request)
    {
        $keys = [
            'bot_token', 'webhook_url', 'default_chat_id', 'report_chat_id',
            'alert_chat_id', 'daily_report_time', 'low_stock_notification',
            'daily_report_enabled', 'transaction_notification', 'welcome_message',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                TelegramSetting::setValue($key, $request->input($key));
            }
        }

        // Handle toggles (checkboxes that might be missing from request)
        $toggles = ['low_stock_notification', 'daily_report_enabled', 'transaction_notification'];
        foreach ($toggles as $toggle) {
            TelegramSetting::setValue($toggle, $request->has($toggle) ? '1' : '0');
        }

        return back()->with('success', 'Pengaturan Telegram berhasil disimpan.');
    }

    public function setWebhook()
    {
        // Reinitialize service with latest DB values
        $telegram = new TelegramService();
        $result = $telegram->setWebhook();

        if ($result) {
            return back()->with('success', '✅ Webhook berhasil didaftarkan ke Telegram.');
        }

        return back()->with('error', '❌ Gagal mendaftarkan webhook. Pastikan Bot Token dan URL Webhook sudah benar dan URL menggunakan HTTPS.');
    }

    public function testBot()
    {
        $chatId = TelegramSetting::getValue('default_chat_id');

        if (empty($chatId)) {
            return back()->with('error', 'Default Chat ID belum diisi.');
        }

        $telegram = new TelegramService();
        $result = $telegram->testConnection($chatId);

        if ($result) {
            return back()->with('success', '✅ Test berhasil! Pesan telah dikirim ke Telegram.');
        }

        return back()->with('error', '❌ Gagal mengirim pesan test. Periksa Bot Token dan Chat ID.');
    }

    public function general()
    {
        return view('settings.general');
    }
}
