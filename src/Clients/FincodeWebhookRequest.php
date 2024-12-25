<?php

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Models\FinWebhook;
use Fincode\OpenAPI\Model\FincodeEvent;
use Fincode\OpenAPI\Model\WebhookSettingCreatingRequest;
use Fincode\OpenAPI\Model\WebhookSettingCreatingResponse;
use Fincode\OpenAPI\Model\WebhookSettingDeletingResponse;
use Fincode\OpenAPI\Model\WebhookSettingListRetrievingResponse;
use Fincode\OpenAPI\Model\WebhookSettingRetrievingResponse;
use Fincode\OpenAPI\Model\WebhookSettingUpdatingRequest;
use Fincode\OpenAPI\Model\WebhookSettingUpdatingResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class FincodeWebhookRequest extends FincodeAbstract
{
    /**
     * Webhook 一覧取得
     *
     * @return Collection<FinWebhook>
     *
     * @see https://docs.fincode.jp/api#tag/Webhook/operation/retrieveWebhookSettingList
     */
    public function list(): Collection
    {
        $response = $this->dispatch(
            WebhookSettingListRetrievingResponse::class,
            fn () => $this->token->webhook()->retrieveWebhookSettingList($this->token->tenant_id),
        );

        $list = Collection::make();

        foreach ($response->getList() as $item) {
            $list->push($this->binding->webhook($item, $this->token->tenant_id));
        }

        return $list;
    }

    /**
     * Webhook 登録
     */
    public function create(
        FincodeEvent|string $event,
        ?string $signature = null,
    ): FinWebhook {
        $event = $event instanceof FincodeEvent ? $event : FincodeEvent::tryFrom($event);

        $signature = $signature ?: Str::random(60);

        $body = (new WebhookSettingCreatingRequest($this->binding->castArray([
            'url' => route('fincode.webhook', ['shop' => $this->token->tenant_id, 'event' => $event->value]),
            'event' => $event->value,
            'signature' => $signature,
        ])));

        $response = $this->dispatch(
            WebhookSettingCreatingResponse::class,
            fn () => $this->token->webhook()->createWebhookSetting($this->token->tenant_id, $body),
        );

        return $this->binding->webhook($response, $this->token->tenant_id ?? $this->token->shop_id);
    }

    /**
     * Webhook 取得
     */
    public function retrieve(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        $response = $this->dispatch(
            WebhookSettingRetrievingResponse::class,
            fn () => $this->token->webhook()->retrieveWebhookSetting($webhook_id, $this->token->tenant_id),
        );

        return $this->binding->webhook($response, $this->token->tenant_id ?? $this->token->shop_id);
    }

    /**
     * Webhook 更新
     */
    public function update(
        FinWebhook $webhook,
        ?string $signature = null,
    ): FinWebhook {
        $signature = $signature ?: Str::random(60);

        $body = (new WebhookSettingUpdatingRequest($this->binding->castArray([
            'url' => route('fincode.webhook', ['shop' => $webhook->shop_id, 'signature' => $signature]),
            'signature' => $signature,
            'event' => $webhook->event->value,
        ])));

        $response = $this->dispatch(
            WebhookSettingUpdatingResponse::class,
            fn () => $this->token->webhook()->updateWebhookSetting($webhook->id, $this->token->tenant_id, $body),
        );

        return $this->binding->webhook($response, $this->token->tenant_id);
    }

    /**
     * Webhook 削除
     */
    public function delete(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        $response = $this->dispatch(
            WebhookSettingDeletingResponse::class,
            fn () => $this->token->webhook()->deleteWebhookSetting($webhook_id, $this->token->tenant_id ?? $this->token->shop_id),
        );

        return FinWebhook::find($response->getId());
    }
}
