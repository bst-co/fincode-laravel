<?php

namespace Fincode\Laravel\Services;

use Exception;
use Fincode\Laravel\Events\FincodeWebhookCompleteEvent;
use Fincode\Laravel\Models\FinWebhook;
use Fincode\Laravel\Services\Webhook\CardWebhook;
use Fincode\Laravel\Services\Webhook\PaymentApplePayWebhook;
use Fincode\Laravel\Services\Webhook\PaymentCardWebhook;
use Fincode\Laravel\Services\Webhook\PaymentKonbiniWebhook;
use Fincode\Laravel\Services\Webhook\WebhookServiceInstance;
use Fincode\OpenAPI\Model\FincodeEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

readonly class WebhookService
{
    public function __construct(
        public FinWebHook $webhook,
        array $payload,
        public ?string $process_id = null,
    ) {
        $this->payload = collect($payload);
        $this->event = FincodeEvent::tryFrom($payload['event']);
    }

    public FincodeEvent $event;

    public Collection $payload;

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $model = tap($this->instance()->handle(), function (Model $model) {
            $model->save();
        });

        FincodeWebhookCompleteEvent::dispatch($this->webhook, $model, $this->process_id);
    }

    private function instance(): WebhookServiceInstance
    {
        return match ($this->event) {
            // 決済 - カード
            FincodeEvent::PAYMENTS_CARD_REGIST,
            FincodeEvent::PAYMENTS_CARD_EXEC,
            FincodeEvent::PAYMENTS_CARD_CAPTURE,
            FincodeEvent::PAYMENTS_CARD_CANCEL,
            FincodeEvent::PAYMENTS_CARD_AUTH,
            FincodeEvent::PAYMENTS_CARD_CHANGE => new PaymentCardWebhook($this),
            // 決済 - ApplePay
            FincodeEvent::PAYMENTS_APPLEPAY_REGIST,
            FincodeEvent::PAYMENTS_APPLEPAY_EXEC,
            FincodeEvent::PAYMENTS_APPLEPAY_CAPTURE,
            FincodeEvent::PAYMENTS_APPLEPAY_CANCEL => new PaymentApplePayWebhook($this),
            // 決済 - コンビニ
            FincodeEvent::PAYMENTS_KONBINI_REGIST,
            FincodeEvent::PAYMENTS_KONBINI_EXEC,
            FincodeEvent::PAYMENTS_KONBINI_CANCEL,
            FincodeEvent::PAYMENTS_KONBINI_COMPLETE,
            FincodeEvent::PAYMENTS_KONBINI_COMPLETE_STUB,
            FincodeEvent::PAYMENTS_KONBINI_EXPIRED_UPDATE_BATCH => new PaymentKonbiniWebhook($this),
            // 決済 - PayPay
            FincodeEvent::PAYMENTS_PAYPAY_REGIST,
            FincodeEvent::PAYMENTS_PAYPAY_EXEC,
            FincodeEvent::PAYMENTS_PAYPAY_CAPTURE,
            FincodeEvent::PAYMENTS_PAYPAY_CANCEL,
            FincodeEvent::PAYMENTS_PAYPAY_CHANGE,
            FincodeEvent::PAYMENTS_PAYPAY_COMPLETE => throw new Exception('To be implemented'),
            // 決済 - 口座振替
            FincodeEvent::PAYMENTS_DIRECTDEBIT_REGIST,
            FincodeEvent::PAYMENTS_DIRECTDEBIT_EXEC,
            FincodeEvent::PAYMENTS_DIRECTDEBIT_CANCEL,
            FincodeEvent::PAYMENTS_DIRECTDEBIT_CHANGE,
            FincodeEvent::PAYMENTS_DIRECTDEBIT_COMPLETE,
            FincodeEvent::PAYMENTS_DIRECTDEBIT_COMPLETE_STUB => throw new Exception('To be implemented'),
            // 3Dセキュア
            FincodeEvent::PAYMENTS_CARD_SECURE2_AUTHENTICATE,
            FincodeEvent::PAYMENTS_CARD_SECURE2_RESULT,
            FincodeEvent::PAYMENTS_CARD_SECURE => new PaymentCardWebhook($this),
            // 決済手段
            FincodeEvent::CUSTOMERS_PAYMENT_METHODS_UPDATED => throw new Exception('To be implemented'),
            // 決済手段 - 契約状況
            FincodeEvent::CONTRACTS_STATUS_CODE_UPDATED => throw new Exception('To be implemented'),
            // カード
            FincodeEvent::CARD_REGIST,
            FincodeEvent::CARD_UPDATE => new CardWebhook($this),
            // サブスクリプション - カード決済
            FincodeEvent::SUBSCRIPTION_CARD_REGIST,
            FincodeEvent::SUBSCRIPTION_CARD_DELETE,
            FincodeEvent::SUBSCRIPTION_CARD_UPDATE => throw new Exception('To be implemented'),
            // サブスクリプション - 口座振替
            FincodeEvent::SUBSCRIPTION_DIRECTDEBIT_REGIST,
            FincodeEvent::SUBSCRIPTION_DIRECTDEBIT_DELETE,
            FincodeEvent::SUBSCRIPTION_DIRECTDEBIT_UPDATE => throw new Exception('To be implemented'),
            // 一括決済 - カード決済
            FincodeEvent::PAYMENTS_BULK_CARD_REGIST,
            FincodeEvent::PAYMENTS_BULK_CARD_BATCH,
            // サブスクリプション課金通知 - カード決済
            FincodeEvent::RECURRING_CARD_BATCH => throw new Exception('To be implemented'),
            // サブスクリプション課金通知 - 口座振替
            FincodeEvent::RECURRING_DIRECTDEBIT_BATCH => throw new Exception('To be implemented'),
        };
    }
}
