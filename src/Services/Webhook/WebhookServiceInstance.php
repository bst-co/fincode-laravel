<?php

namespace Fincode\Laravel\Services\Webhook;

use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\Laravel\Exceptions\FincodeWebhookTargetException;
use Fincode\Laravel\Services\WebhookService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class WebhookServiceInstance
{
    /**
     * 初期化処理
     */
    public function __construct(
        protected readonly WebhookService $service,
    ) {
        $this->payload = $this->service->payload;
        $this->binding = new FinModelBinding;
    }

    protected readonly Collection $payload;

    protected readonly FinModelBinding $binding;

    /**
     * 処理実行
     *
     * @throws FincodeWebhookTargetException
     */
    abstract public function handle(): Model;
}
