<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinWebhook;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\DeleteFlag;
use OpenAPI\Fincode\Model\FincodeEvent;
use OpenAPI\Fincode\Model\WebhookSettingCreatingRequest;
use OpenAPI\Fincode\Model\WebhookSettingCreatingResponse;
use OpenAPI\Fincode\Model\WebhookSettingDeletingResponse;
use OpenAPI\Fincode\Model\WebhookSettingListRetrievingResponse;
use OpenAPI\Fincode\Model\WebhookSettingUpdatingRequest;
use OpenAPI\Fincode\Model\WebhookSettingUpdatingResponse;

class FincodeWebhookRequest extends FincodeAbstract
{
    /**
     * Webhook 一覧取得
     *
     * @return Collection<FinWebhook>
     *
     * @throws FincodeUnknownResponseException
     *
     * @see https://docs.fincode.jp/api#tag/Webhook/operation/retrieveWebhookSettingList
     */
    public function list(): Collection
    {
        try {
            $response = $this->token->webhook()
                ->retrieveWebhookSettingList(null);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof WebhookSettingListRetrievingResponse) {
            $list = Collection::make();

            foreach ($response->getList() as $item) {
                $list->push($this->binding->webhook($item, $this->token->shop_id));
            }

            return $list;
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * Webhook 登録
     *
     * @throws FincodeUnknownResponseException
     */
    public function create(
        FincodeEvent|string $event,
        ?string $signature = null,
    ): FinWebhook {
        $event = $event instanceof FincodeEvent ? $event : FincodeEvent::tryFrom($event);

        $signature = $signature ?: Str::random(60);

        $body = (new WebhookSettingCreatingRequest($this->binding->castArray([
            'url' => route('fincode.webhook', ['shop' => $this->token->shop_id, 'signature' => $signature]),
            'event' => $event->value,
            'signature' => $signature,
        ])));

        try {
            $response = $this->token->webhook()
                ->createWebhookSetting(null, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof WebhookSettingCreatingResponse) {
            return $this->binding->webhook($response, $this->token->shop_id);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * Webhook 取得
     *
     * @throws FincodeUnknownResponseException
     */
    public function retrieve(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        try {
            $response = $this->token->webhook()
                ->retrieveWebhookSetting($webhook_id, null);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof WebhookSettingListRetrievingResponse) {
            return $this->binding->webhook($response, $this->token->shop_id);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * Webhook 更新
     *
     * @throws FincodeUnknownResponseException
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

        try {
            $response = $this->token->webhook()
                ->updateWebhookSetting($webhook->id, null, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof WebhookSettingUpdatingResponse) {
            return $this->binding->webhook($response, $this->token->shop_id);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * Webhook 削除
     *
     * @throws FincodeUnknownResponseException
     */
    public function delete(FinWebhook|string $webhook): FinWebhook
    {
        $webhook_id = $webhook instanceof FinWebhook ? $webhook->id : $webhook;

        try {
            $response = $this->token->webhook()
                ->deleteWebhookSetting($webhook_id, null);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof WebhookSettingDeletingResponse && $response->getDeleteFlag() === DeleteFlag::_1) {
            return FinWebhook::find($response->getId());
        }

        throw new FincodeUnknownResponseException;
    }
}
