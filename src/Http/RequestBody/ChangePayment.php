<?php

namespace Fincode\Laravel\Http\RequestBody;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Http\FincodeRequestToken;
use Fincode\Laravel\Models\FinPayment;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CardPaymentJobCode;
use OpenAPI\Fincode\Model\ChangeAmountOfPayment200Response;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PaymentCardChangingAmountRequest;
use OpenAPI\Fincode\Model\PaymentDirectDebitChangingAmountRequest;
use OpenAPI\Fincode\Model\PaymentPayPayChangingAmountRequest;

class ChangePayment extends PaymentBase
{
    public function __construct(
        FincodeRequestToken $token,
        public readonly FinPayment $model,
        public readonly int $amount,
        public readonly int $tax = 0,
    ) {
        parent::__construct($token);
    }

    /**
     * カード決済 金額変更
     *
     * @param  CardPaymentJobCode  $job_code  a
     * @return PaymentMethod<PaymentCardChangingAmountRequest>
     */
    public function card(
        CardPaymentJobCode $job_code
    ): PaymentMethod {
        return new PaymentMethod($this, PaymentCardChangingAmountRequest::class, [
            'job_code' => $job_code->value,
        ]);
    }

    public function payPay(
        ?string $update_description = null,
    ): PaymentMethod {
        return new PaymentMethod($this, PaymentPayPayChangingAmountRequest::class, [
            'update_description' => $update_description,
        ]);
    }

    public function directDebit(): PaymentMethod
    {
        return new PaymentMethod($this, PaymentDirectDebitChangingAmountRequest::class, []);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'pay_type' => $this->model->pay_type?->value,
            'access_id' => $this->model->access_id,
            'amount' => sprintf('%d', $this->amount),
            'tax' => sprintf('%d', $this->tax),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws FincodeUnknownResponseException
     */
    public function exec(ModelInterface $body): FinPayment
    {
        try {
            $response = $this->token->default()
                ->changeAmountOfPayment($this->model->id, null, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof ChangeAmountOfPayment200Response) {
            return $this->binding->payment($response);
        }

        throw new FincodeUnknownResponseException;
    }
}