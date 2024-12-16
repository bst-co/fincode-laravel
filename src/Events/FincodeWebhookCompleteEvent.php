<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Webhook処理が完了したことを通知するイベント
 */
class FincodeWebhookCompleteEvent
{
    use Dispatchable;

    /**
     * @param  FinWebhook  $webhook  対象のWebhookイベント
     * @param  Model  $model  変更されたモデル
     * @param  ?string  $process_id  処理番号を示す一意のID
     */
    public function __construct(
        public readonly FinWebhook $webhook,
        public readonly Model $model,
        public readonly ?string $process_id = null,
    ) {}
}
