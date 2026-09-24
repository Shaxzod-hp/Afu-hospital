<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramTest extends Command
{
    protected $signature = 'telegram:test {--send : Guruhga haqiqiy test xabar yuborish}';

    protected $description = 'Telegram bot sozlamalarini va serverdan api.telegram.org ga ulanishni tekshiradi';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (!$token || !$chatId) {
            $this->error('TELEGRAM_BOT_TOKEN yoki TELEGRAM_GROUP_CHAT_ID .env da yo\'q (yoki config:cache eskirgan — `php artisan config:clear`).');
            return self::FAILURE;
        }
        $this->info('Sozlamalar topildi.');

        try {
            $me = Http::timeout(10)->withOptions(['force_ip_resolve' => 'v4'])->get("https://api.telegram.org/bot{$token}/getMe")->json();
        } catch (\Throwable $e) {
            $this->error('api.telegram.org ga ulanib bo\'lmadi: ' . $e->getMessage());
            $this->line('Hosting chiquvchi HTTPS so\'rovlarni bloklayotgan bo\'lishi mumkin — provayderga murojaat qiling.');
            return self::FAILURE;
        }

        if (!($me['ok'] ?? false)) {
            $this->error('Bot tokeni noto\'g\'ri: ' . ($me['description'] ?? 'noma\'lum xato'));
            return self::FAILURE;
        }
        $this->info('Bot: @' . $me['result']['username']);

        $member = Http::timeout(10)->withOptions(['force_ip_resolve' => 'v4'])->get("https://api.telegram.org/bot{$token}/getChatMember", [
            'chat_id' => $chatId,
            'user_id' => $me['result']['id'],
        ])->json();

        if (!($member['ok'] ?? false)) {
            $this->error('Guruh topilmadi yoki bot guruhda emas: ' . ($member['description'] ?? ''));
            return self::FAILURE;
        }
        $this->info('Botning guruhdagi holati: ' . $member['result']['status']);

        if ($this->option('send')) {
            $res = Http::timeout(10)->withOptions(['force_ip_resolve' => 'v4'])->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => '✅ Test xabar: sayt serveridan Telegram bildirishnomalari ishlayapti.',
            ])->json();

            if (!($res['ok'] ?? false)) {
                $this->error('Xabar yuborilmadi: ' . ($res['description'] ?? ''));
                return self::FAILURE;
            }
            $this->info('Test xabar guruhga yuborildi.');
        }

        return self::SUCCESS;
    }
}
