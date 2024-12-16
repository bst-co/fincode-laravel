<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Foundation\Events\Dispatchable;

class FincodeWebhookEvent
{
    use Dispatchable;

    /**
     * @param  FinWebhook  $webhook  対象のWebhookイベント
     * @param  array  $payload  送信された値
     */
    public function __construct(
        public readonly FInWebHook $webhook,
        public readonly array $payload,
    ) {}
}
