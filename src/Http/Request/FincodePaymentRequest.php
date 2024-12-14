<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Http\RequestBody\ChangePayment;
use Fincode\Laravel\Http\RequestBody\CreatePayment;
use Fincode\Laravel\Http\RequestBody\ExecutePayment;
use Fincode\Laravel\Models\FinPayment;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CancelPayment200Response;
use OpenAPI\Fincode\Model\CancelPaymentRequest;
use OpenAPI\Fincode\Model\CardPayMethod;
use OpenAPI\Fincode\Model\PaymentCardReauthorizingRequest;
use OpenAPI\Fincode\Model\PayType;

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
     * @param  int  $amount  購入金額小径
     * @param  int  $tax  付加消費税額
     */
    public function create(
        int $amount,
        int $tax = 0,
        ?string $id = null,
    ): CreatePayment {
        return new CreatePayment($this->token, $amount, $tax, $id);
    }

    /**
     * 決済 確定
     */
    public function execute(FinPayment $payment): ExecutePayment
    {
        return new ExecutePayment($this->token, $payment);
    }

    /**
     * 決済 取得
     *
     * @throws FincodeUnknownResponseException
     */
    public function get()
    {
        throw new FincodeUnknownResponseException;
    }

    public function capture(FinPayment $payment) {}

    /**
     * 決済 再オーソリ
     *
     * @throws FincodeUnknownResponseException
     */
    public function authorize(
        FinPayment $payment,
        CardPayMethod $method = CardPayMethod::_1,
        ?PayType $pay_times = null
    ): FinPayment {
        $body = new PaymentCardReauthorizingRequest([
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
            'method' => $method->value,
            'pay_times' => $pay_times?->value,
        ]);

        try {
            $response = $this->token->default()
                ->authorizePayment($payment->id, null, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof PaymentCardReauthorizingRequest) {
            return $this->binding->payment($response);
        }
        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済キャンセル処理を実行
     *
     * @throws FincodeUnknownResponseException
     */
    public function cancel(FinPayment $payment): FinPayment
    {
        $body = new CancelPaymentRequest([
            'pay_type' => $payment->pay_type->value,
            'access_id' => $payment->access_id,
        ]);

        try {
            $response = $this->token->default()
                ->cancelPayment($payment->id, null, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof CancelPayment200Response) {
            return $this->binding->payment($response);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 金額変更
     *
     * @throws FincodeUnknownResponseException
     */
    public function change(
        FinPayment $payment,
        int $amount,
        int $tax = 0,
    ) {
        return new ChangePayment($this->token, $payment, $amount, $tax);
    }

    /**
     * 決済 認証後決済実行
     */
    public function secure()
    {

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 バーコード発行
     */
    public function barcode()
    {
        throw new FincodeUnknownResponseException;
    }
}
