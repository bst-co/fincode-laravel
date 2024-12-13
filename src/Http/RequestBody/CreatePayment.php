<?php

namespace Fincode\Laravel\Http\RequestBody;

use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\Laravel\Http\FincodeRequestToken;
use Illuminate\Contracts\Support\Arrayable;
use OpenAPI\Fincode\Model\ApplePayPaymentJobCode;
use OpenAPI\Fincode\Model\CardPaymentJobCode;
use OpenAPI\Fincode\Model\CreatePayment200Response;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PaymentApplePayCreatingRequest;
use OpenAPI\Fincode\Model\PaymentCardCreatingRequest;
use OpenAPI\Fincode\Model\PaymentDirectDebitCreatingRequest;
use OpenAPI\Fincode\Model\PaymentKonbiniCreatingRequest;
use OpenAPI\Fincode\Model\PaymentPayPayCreatingRequest;
use OpenAPI\Fincode\Model\PaymentVirtualAccountCreatingRequest;
use OpenAPI\Fincode\Model\PayPayPaymentJobCode;
use OpenAPI\Fincode\Model\PayType;

readonly class CreatePayment implements Arrayable
{
    public FinModelBinding $binding;

    /**
     * @param  int  $amount  利用金額
     * @param  int  $tax  税送料
     */
    public function __construct(
        public FincodeRequestToken $token,
        public int $amount,
        public int $tax = 0,
        public ?string $id = null,
    ) {
        $this->binding = new FinModelBinding;
    }

    /**
     * クレジットカード決済を利用する
     *
     * @return CreatePaymentMethod<PaymentApplePayCreatingRequest>
     */
    public function card(CardPaymentJobCode $job_code): CreatePaymentMethod
    {
        return new CreatePaymentMethod($this, PaymentCardCreatingRequest::class, PayType::CARD, [
            'job_code' => $job_code,
        ]);
    }

    /**
     * ApplePay決済
     *
     * @return CreatePaymentMethod<PaymentApplePayCreatingRequest>
     */
    public function applyPay(ApplePayPaymentJobCode $job_code, ?string $item_code = null): CreatePaymentMethod
    {
        return new CreatePaymentMethod($this, PaymentApplePayCreatingRequest::class, PayType::APPLEPAY, [
            'job_code' => $job_code,
            'item_code' => $item_code,
        ]);
    }

    /**
     * コンビニ決済
     *
     * @return CreatePaymentMethod<PaymentKonbiniCreatingRequest>
     */
    public function convenience(): CreatePaymentMethod
    {
        return new CreatePaymentMethod($this, PaymentKonbiniCreatingRequest::class, PayType::KONBINI);
    }

    /**
     * PayPay決済
     *
     * @return CreatePaymentMethod<PaymentPaypayCreatingRequest>
     */
    public function payPay(PayPayPaymentJobCode $job_code, ?string $order_description = null): CreatePaymentMethod
    {
        return new CreatePaymentMethod($this, PaymentPaypayCreatingRequest::class, PayType::PAYPAY, [
            'job_code' => $job_code,
            'order_description' => $order_description,
        ]);
    }

    /**
     * 口座振替決済
     *
     * @return CreatePaymentMethod<PaymentDirectDebitCreatingRequest>
     */
    public function directDebit(?string $remarks = null): CreatePaymentMethod
    {
        return new CreatePaymentMethod($this, PaymentDirectDebitCreatingRequest::class, PayType::DIRECTDEBIT, [
            'remarks' => $remarks,
        ]);
    }

    /**
     * 銀行振込(バーチャル口座)決済
     *
     * @return CreatePaymentMethod<PaymentVirtualAccountCreatingRequest>
     */
    public function virtualAccount(): CreatePaymentMethod
    {
        $array = $this->toArray();

        return new CreatePaymentMethod($this, PaymentVirtualAccountCreatingRequest::class, PayType::VIRTUALACCOUNT, [
            'billing_amount' => $array['amount'],
            'billing_tax' => $array['tax'],
        ]);
    }

    final public function exec(ModelInterface $body)
    {
        $response = $this->token->default()
            ->createPayment($body);

        if ($response instanceof CreatePayment200Response) {
            $result = $this->binding->payment();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'amount' => (string) max(0, $this->amount),
            'tax' => (string) max($this->tax),
        ];
    }
}
