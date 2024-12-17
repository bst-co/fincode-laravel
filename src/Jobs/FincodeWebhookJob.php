<?php

namespace Fincode\Laravel\Jobs;

use Exception;
use Fincode\Laravel\Models\FinWebhook;
use Fincode\Laravel\Services\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FincodeWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  FinWebhook  $webhook  対象のWebhookイベント
     * @param  array  $payload  送信された値
     * @param  string  $process_id  処理番号を示す一意のID
     */
    public function __construct(
        private readonly FInWebHook $webhook,
        private readonly array $payload,
        private readonly string $process_id,
    ) {}

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        (new WebhookService($this->webhook, $this->payload, $this->process_id))();
    }
}
