<?php

namespace Fincode\Laravel\Http\RequestBody;

use Carbon\Carbon;
use DateTimeInterface;
use Fincode\Laravel\Clients\FincodeRequestToken;
use Fincode\Laravel\Exceptions\FincodeApiException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinPayment;
use Fincode\OpenAPI\ApiException;
use Fincode\OpenAPI\Model\ExecutePayment200Response;
use Fincode\OpenAPI\Model\ModelInterface;
use Fincode\OpenAPI\Model\PaymentCardExecutingRequest;
use Fincode\OpenAPI\Model\PaymentDirectDebitExecutingRequest;
use Fincode\OpenAPI\Model\PaymentKonbiniExecutingRequest;
use Fincode\OpenAPI\Model\PaymentPayPayExecutingRequest;
use Fincode\OpenAPI\Model\PaymentVirtualAccountExecutingRequest;
use Fincode\OpenAPI\Model\RedirectType;
use Fincode\OpenAPI\Model\WinSizeType;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Model;

/**
 * @see https://docs.fincode.jp/api#tag/%E6%B1%BA%E6%B8%88/operation/executePayment
 */
class ExecutePayment extends PaymentBase
{
    public function __construct(
        FincodeRequestToken $token,
        public readonly FinPayment $model
    ) {
        parent::__construct($token);
    }

    public function cardByToken(string $token): ExecutePaymentMethod
    {
        return new ExecutePaymentMethod($this, PaymentCardExecutingRequest::class, [
            'token' => $token,
        ]);
    }

    public function cardById(FinCard|string $card_id): ExecutePaymentMethod
    {
        return new ExecutePaymentMethod($this, PaymentCardExecutingRequest::class, [
            'card_id' => $card_id instanceof FinCard ? $card_id->id : $card_id,
        ]);
    }

    /**
     * ApplePay決済 実行
     *
     * @param  string  $token  ApplePay カードトークン
     */
    public function applePay(string $token): ExecutePaymentMethod
    {
        return new ExecutePaymentMethod($this, PaymentCardExecutingRequest::class, [
            'token' => $token,
        ]);
    }

    /**
     * コンビニ決済 実行
     *
     * @param  string  $device_name  デバイス名
     * @param  int  $win_width  デバイス画面幅
     * @param  int  $win_height  デバイス画面の高さ
     * @param  float  $pixel_ratio  デバイスピクセル比
     * @param  WinSizeType  $win_size_type  画面サイズ種別
     * @param  ?string  $payment_term_day  支払い期限日数
     */
    public function convenience(
        string $device_name,
        int $win_width,
        int $win_height,
        float $pixel_ratio,
        WinSizeType $win_size_type,
        ?string $payment_term_day = null
    ): ExecutePaymentMethod {
        return new ExecutePaymentMethod($this, PaymentKonbiniExecutingRequest::class, [
            'device_name' => $device_name,
            'win_width' => sprintf('%d', $win_width),
            'win_height' => sprintf('%d', $win_height),
            'pixel_ratio' => sprintf('%.2f', $pixel_ratio),
            'win_size_type' => $win_size_type->value,
            'payment_term_day' => $payment_term_day,
        ]);
    }

    /**
     * PayPay決済 実行
     *
     * @param  string  $redirect_url  リダイレクトURL
     * @param  RedirectType|null  $redirect_type  リダイレクト先種別
     * @param  string|null  $user_agent  ユーザーエージェント
     */
    public function payPay(
        string $redirect_url,
        ?RedirectType $redirect_type = null,
        ?string $user_agent = null
    ): ExecutePaymentMethod {
        return new ExecutePaymentMethod($this, PaymentPayPayExecutingRequest::class, [
            'redirect_url' => $redirect_url,
            'redirect_type' => $redirect_type?->value,
            'user_agent' => $user_agent,
        ]);
    }

    public function directDebit(
        ?string $payment_method_id = null,
        DateTimeInterface|string|null $target_date = null,
    ): ExecutePaymentMethod {
        return new ExecutePaymentMethod($this, PaymentDirectDebitExecutingRequest::class, [
            'payment_method_id' => $payment_method_id,
            'target_date' => $target_date ? Carbon::parse($target_date)->format('Y/m/d') : null,
        ]);
    }

    /**
     * バーチャル口座決済 実行
     *
     * @param  int|null  $payment_term_day  支払期限日数
     * @param  string|null  $reference_order_id  バーチャル口座 再利用 オーダーID
     * @return ExecutePaymentMethod<PaymentVirtualAccountExecutingRequest>
     */
    public function virtualAccount(
        ?int $payment_term_day = null,
        ?string $reference_order_id = null,
    ): ExecutePaymentMethod {
        return new ExecutePaymentMethod($this, PaymentVirtualAccountExecutingRequest::class, [
            'payment_term_day' => sprintf('%d', $payment_term_day),
            'reference_order_id' => $reference_order_id,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'pay_type' => $this->model->pay_type?->value,
            'access_id' => $this->model->access_id,
            'customer_id' => $this->model->customer_id,
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws FincodeUnknownResponseException
     */
    public function exec(ModelInterface $body): Model
    {
        try {
            $response = $this->token->default()
                ->executePayment($this->model->id, $this->token->tenant_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeApiException($e);
        }

        if ($response instanceof ExecutePayment200Response) {
            return $this->binding->payment($response);
        }

        throw new FincodeUnknownResponseException;
    }
}
