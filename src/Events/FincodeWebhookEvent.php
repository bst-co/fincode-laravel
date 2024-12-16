<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Webhook処理がリクエストされたことを通知するイベント
 */
class FincodeWebhookEvent
{
    use Dispatchable;

    /**
     * @param  string  $process_id  処理番号を示す一意のID
     * @param  FinWebhook  $webhook  対象のWebhookイベント
     * @param  array  $payload  送信された値
     */
    public function __construct(
        public readonly string $process_id,
        public readonly FInWebHook $webhook,
        public readonly array $payload,
    ) {}
}
