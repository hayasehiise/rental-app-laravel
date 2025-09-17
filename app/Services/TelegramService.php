<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $chatId;

    public function __construct(?string $botToken = null, ?string $chatId = null)
    {
        $this->botToken = $botToken ?: config('services.telegram.bot_token');
        $this->chatId = $chatId ?: config('services.telegram.chat_id');
    }

    public function send(string $message, ?string $chatId = null, string $parseMode = 'HTML'): Response
    {
        $chatId = $chatId ?? $this->chatId;
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $payload = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => true
        ];

        $res = Http::timeout(30)->post($url, $payload);

        if ($res->failed()) {
            Log::error("Telegram send Failed", [
                'chat_id' => $chatId,
                'payload' => $payload,
                'response' => $res->body(),
            ]);
        }

        return $res;
    }
}
