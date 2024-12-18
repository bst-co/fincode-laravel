<?php

namespace Fincode\Laravel\Clients;

use ErrorException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinPayment;
use Fincode\OpenAPI\Model\CancelPayment200Response;
use Fincode\OpenAPI\Model\CapturePayment200Response;
use Fincode\OpenAPI\Model\CardPayMethod;
use Fincode\OpenAPI\Model\CreatePayment200Response;
use Fincode\OpenAPI\Model\ExecutePayment200Response;
use Fincode\OpenAPI\Model\PaymentApplePayCancelingRequest;
use Fincode\OpenAPI\Model\PaymentApplePayCreatingRequest;
use Fincode\OpenAPI\Model\PaymentApplePayExecutingRequest;
use Fincode\OpenAPI\Model\PaymentCardCancelingRequest;
use Fincode\OpenAPI\Model\PaymentCardChangingAmountRequest;
use Fincode\OpenAPI\Model\PaymentCardCreatingRequest;
use Fincode\OpenAPI\Model\PaymentCardExecutingRequest;
use Fincode\OpenAPI\Model\PaymentCardReauthorizingRequest;
use Fincode\OpenAPI\Model\PaymentDirectDebitCancelingRequest;
use Fincode\OpenAPI\Model\PaymentDirectDebitChangingAmountRequest;
use Fincode\OpenAPI\Model\PaymentDirectDebitCreatingRequest;
use Fincode\OpenAPI\Model\PaymentDirectDebitExecutingRequest;
use Fincode\OpenAPI\Model\PaymentKonbiniCancelingRequest;
use Fincode\OpenAPI\Model\PaymentKonbiniCreatingRequest;
use Fincode\OpenAPI\Model\PaymentKonbiniExecutingRequest;
use Fincode\OpenAPI\Model\PaymentPayPayCancelingRequest;
use Fincode\OpenAPI\Model\PaymentPayPayChangingAmountRequest;
use Fincode\OpenAPI\Model\PaymentPayPayCreatingRequest;
use Fincode\OpenAPI\Model\PaymentPayPayExecutingRequest;
use Fincode\OpenAPI\Model\PaymentRetrievingQueryParams;
use Fincode\OpenAPI\Model\PaymentVirtualAccountCancelingRequest;
use Fincode\OpenAPI\Model\PaymentVirtualAccountCreatingRequest;
use Fincode\OpenAPI\Model\PaymentVirtualAccountExecutingRequest;
use Fincode\OpenAPI\Model\PayType;
use Fincode\OpenAPI\Model\RetrievePayment200Response;

/**
 * Fincode決済APIリクエスト
 *
 * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88
 */
class FincodePaymentRequest extends FincodeAbstract
{
    /**
     * 決済 一覧取得
     *
     * @throws FincodeUnknownResponseException
     */
    public function index()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 登録
     *
     * @param  PayType  $pay_type  決済種別
     * @param  int  $amount  購入金額小径
     * @param  int  $tax  付加消費税額
     * @param  ?string  $id  注文ID(任意のIDを発行する場合)
     * @param  string[]  $attributes  追加パラメータ
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/createPayment
     */
    public function create(
        PayType $pay_type,
        int $amount,
        int $tax = 0,
        ?string $id = null,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $pay_type->value,
            'id' => $id,
            'client_field_1' => $this->token->client_field_1,
            'client_field_3' => $this->token->client_field_3,
        ];

        if ($pay_type === PayType::VIRTUALACCOUNT) {
            $attributes['billing_amount'] = sprintf('%d', $amount);
            $attributes['billing_tax'] = $tax ? sprintf('%d', $tax) : null;
        } else {
            $attributes['amount'] = sprintf('%d', $amount);
            $attributes['tax'] = $tax ? sprintf('%d', $tax) : null;
        }

        $values = $this->binding->castArray($attributes);

        $body = match ($pay_type) {
            PayType::CARD => new PaymentCardCreatingRequest($values),
            PayType::APPLEPAY => new PaymentApplePayCreatingRequest($values),
            PayType::KONBINI => new PaymentKonbiniCreatingRequest($values),
            PayType::PAYPAY => new PaymentPaypayCreatingRequest($values),
            PayType::DIRECTDEBIT => new PaymentDirectDebitCreatingRequest($values),
            PayType::VIRTUALACCOUNT => new PaymentVirtualAccountCreatingRequest($values),
        };

        $response = $this->dispatch(
            CreatePayment200Response::class,
            fn () => $this->token->default()->createPayment($this->token->shop_id, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 実行API
     *
     * @param  FinPayment  $payment  決済モデル
     * @param  array<string, string>  $attributes  追加パラメータ
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/executePayment
     */
    public function execute(
        FinPayment $payment,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
            'customer_id' => $payment->customer_id,
        ];

        $values = $this->binding->castArray($attributes);

        $body = match ($payment->pay_type) {
            PayType::CARD => new PaymentCardExecutingRequest($values),
            PayType::APPLEPAY => new PaymentApplePayExecutingRequest($values),
            PayType::KONBINI => new PaymentKonbiniExecutingRequest($values),
            PayType::PAYPAY => new PaymentPayPayExecutingRequest($values),
            PayType::DIRECTDEBIT => new PaymentDirectDebitExecutingRequest($values),
            PayType::VIRTUALACCOUNT => new PaymentVirtualAccountExecutingRequest($values),
        };

        $response = $this->dispatch(
            ExecutePayment200Response::class,
            fn () => $this->token->default()->executePayment($payment->id, $this->token->shop_id, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 取得API
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/retrievePayment
     */
    public function retrieve(
        FinPayment|string $payment,
        PayType $pay_type,
    ): FinPayment {
        $payment_id = $payment instanceof FinPayment ? $payment->id : $payment;

        $queries = [
            'pay_type' => $pay_type->value,
        ];

        $query = new PaymentRetrievingQueryParams($this->binding->castArray($queries));

        $response = $this->dispatch(
            RetrievePayment200Response::class,
            fn () => $this->token->default()->retrievePayment($payment_id, $query, $this->token->shop_id),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 売上確定API
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/capturePayment
     *
     * @throws ErrorException
     */
    public function capture(
        FinPayment $payment,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
        ];

        $values = $this->binding->castArray($attributes);

        $body = match ($payment->pay_type) {
            PayType::CARD => new PaymentCardExecutingRequest($values),
            PayType::APPLEPAY => new PaymentApplePayExecutingRequest($values),
            PayType::PAYPAY => new PaymentPayPayExecutingRequest($values),
            default => throw new ErrorException("{$payment->pay_type} is not supported."),
        };

        $response = $this->dispatch(
            CapturePayment200Response::class,
            fn () => $this->token->default()->capturePayment($payment->id, $this->token->shop_id, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済キャンセル処理を実行
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/cancelPayment
     */
    public function cancel(
        FinPayment $payment,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
        ];

        $values = $this->binding->castArray($attributes);

        $body = match ($payment->pay_type) {
            PayType::CARD => new PaymentCardCancelingRequest($values),
            PayType::APPLEPAY => new PaymentApplePayCancelingRequest($values),
            PayType::PAYPAY => new PaymentPayPayCancelingRequest($values),
            PayType::KONBINI => new PaymentKonbiniCancelingRequest($values),
            PayType::DIRECTDEBIT => new PaymentDirectDebitCancelingRequest($values),
            PayType::VIRTUALACCOUNT => new PaymentVirtualAccountCancelingRequest($values),
        };

        $response = $this->dispatch(
            CancelPayment200Response::class,
            fn () => $this->token->default()->cancelPayment($payment->id, $this->token->shop_id, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 再オーソリ
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/authorizePayment
     *
     * @throws ErrorException
     */
    public function authorize(
        FinPayment $payment,
        CardPayMethod $method = CardPayMethod::_1,
        ?PayType $pay_times = null,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
            'method' => $method->value,
            'pay_times' => $pay_times?->value,
        ];

        $values = $this->binding->castArray($attributes);

        $body = match ($payment->pay_type) {
            PayType::CARD => new PaymentCardReauthorizingRequest($values),
            default => throw new ErrorException("{$payment->pay_type} is not supported."),
        };

        $response = $this->dispatch(
            PaymentCardReauthorizingRequest::class,
            fn () => $this->token->default()->authorizePayment($payment->id, null, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 金額変更
     *
     * @throws ErrorException
     */
    public function change(
        FinPayment $payment,
        int $amount,
        int $tax = 0,
        array $attributes = [],
    ): FinPayment {
        $attributes = [
            ...$attributes,
            'pay_type' => $payment->pay_type->value,
            'amount' => sprintf('%d', $amount),
            'tax' => $tax ? sprintf('%d', $tax) : null,
        ];

        $values = $this->binding->castArray($attributes);

        $body = match ($payment->pay_type) {
            PayType::CARD => new PaymentCardChangingAmountRequest($values),
            PayType::PAYPAY => new PaymentPayPayChangingAmountRequest($values),
            PayType::DIRECTDEBIT => new PaymentDirectDebitChangingAmountRequest($values),
            default => throw new ErrorException("{$payment->pay_type} is not supported."),
        };

        $response = $this->dispatch(
            PaymentCardReauthorizingRequest::class,
            fn () => $this->token->default()->changeAmountOfPayment($payment->id, null, $body),
        );

        return $this->binding->payment($response);
    }

    /**
     * 決済 認証後決済実行
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/executePaymentAfterThreeDSecureecure
     */
    public function secure()
    {

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 バーコード発行
     *
     * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/generateBarcodeOfPayment
     */
    public function barcode()
    {
        throw new FincodeUnknownResponseException;
    }
}
