<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinPayment;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CreatePaymentRequest;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PaymentApplePayCreatingRequest;
use OpenAPI\Fincode\Model\PaymentCard;
use OpenAPI\Fincode\Model\PaymentCardCreatingRequest;
use OpenAPI\Fincode\Model\PaymentPayPayCreatingRequest;
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
     * @throws FincodeUnknownResponseException
     */
    public function index()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 登録
     * @throws FincodeUnknownResponseException
     */
    public function register(
        PayType $pay_type,
        float $amount,
        float $tax = 0.0,
        ModelInterface|array $body = null,
    )
    {
        $body
            ->setPayType($payment->pay_type)
            ->setClientField1($this->token->client_field_1)
            ->setClientField2($this->token->client_field_2)
            ->setTdTenantName($this->token->tenant_name);

        try {
            $response = $this->token->default()
                ->createPayment($this->token->tenant_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 確定
     * @throws FincodeUnknownResponseException
     */
    public function execute(FinPayment $payment)
    {
        throw new FincodeUnknownResponseException;
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

    /**
     * 決済 金額変更
     * @throws FincodeUnknownResponseException
     */
    public function change()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 再オーソリ
     * @throws FincodeUnknownResponseException
     */
    public function authorize()
    {
        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済キャンセル処理を実行
     *
     */
    public function cancel()
    {

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 認証後決済実行
     *
     */
    public function secure()
    {

        throw new FincodeUnknownResponseException;
    }

    /**
     * 決済 バーコード発行
     *
     */
    public function barcode()
    {

        throw new FincodeUnknownResponseException;
    }
