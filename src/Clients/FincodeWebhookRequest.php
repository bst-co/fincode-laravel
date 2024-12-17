<?php

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use OpenAPI\Fincode\Model\FincodeEvent;
use OpenAPI\Fincode\Model\WebhookSettingCreatingRequest;
use OpenAPI\Fincode\Model\WebhookSettingCreatingResponse;
use OpenAPI\Fincode\Model\WebhookSettingDeletingResponse;
use OpenAPI\Fincode\Model\WebhookSettingListRetrievingResponse;
use OpenAPI\Fincode\Model\WebhookSettingRetrievingResponse;
use OpenAPI\Fincode\Model\WebhookSettingUpdatingRequest;
use OpenAPI\Fincode\Model\WebhookSettingUpdatingResponse;

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
            fn () => $this->token->webhook()->retrieveWebhookSettingList(null),
        );

        $list = Collection::make();

        foreach ($response->getList() as $item) {
            $list->push($this->binding->webhook($item, $this->token->shop_id));
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
            'url' => route('fincode.webhook', ['shop' => $this->token->shop_id, 'event' => $event->value]),
            'event' => $event->value,
            'signature' => $signature,
        ])));

        $response = $this->dispatch(
            WebhookSettingCreatingResponse::class,
            fn () => $this->token->webhook()->createWebhookSetting(null, $body),
        );

        return $this->binding->webhook($response, $this->token->shop_id);
    }

    /**
     * Webhook 取得
     */
    public function retrieve(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        $response = $this->dispatch(
            WebhookSettingRetrievingResponse::class,
            fn () => $this->token->webhook()->retrieveWebhookSetting($webhook_id, null),
        );

        return $this->binding->webhook($response, $this->token->shop_id);
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
            fn () => $this->token->webhook()->updateWebhookSetting($webhook->id, null, $body),
        );

        return $this->binding->webhook($response, $this->token->shop_id);
    }

    /**
     * Webhook 削除
     */
    public function delete(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        $response = $this->dispatch(
            WebhookSettingDeletingResponse::class,
            fn () => $this->token->webhook()->deleteWebhookSetting($webhook_id, null),
        );

        return FinWebhook::find($response->getId());
    }
}
