<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Fincode\Laravel\Models\FinPlatform;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use JsonSerializable;
use OpenAPI\Fincode\Model\CardBrand;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PayType;
use OpenAPI\Fincode\Model\ShopType;

class FinModelBinding
{
    /**
     * 値をFinCardモデルに結合する
     *
     * @throws ValidationException
     */
    public function card(FinCard|ModelInterface|array|string $values): FinCard
    {
        $attributes = self::sanitize($values, [
            'id' => ['required', 'string', 'size:25'],
            'customer_id' => ['required', 'string', 'max:60'],
            'default_flat' => ['required', 'boolean'],
            'card_no' => ['required', 'string', 'max:16'],
            'holder_name' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', new Enum(CardBrand::class)],
            'card_no_hash' => ['required', 'string', 'size:64'],
        ]);

        /*
         * モデルを取得、または新規作成
         */
        return tap(FinCard::findOrnew($attributes->input('id')))
            ->forceFill([
                'id' => $attributes->input('id'),
                'customer_id' => $attributes->input('customer_id'),
                'default_flag' => $attributes->boolean('default_flag'),
                'card_no' => $attributes->input('card_no'),
                'expire' => $attributes->input('expire'),
                'holder_name' => $attributes->input('holder_name'),
                'type' => $attributes->input('type'),
                'brand' => $attributes->input('brand'),
                'card_no_hash' => $attributes->input('card_no_hash'),
                'created' => $attributes->input('created') ?? $model->created ?? now(),
                'updated' => $attributes->input('updated'),
            ]);
    }

    /**
     * 値をFinCustomerモデルに結合する
     *
     * @throws ValidationException
     */
    public function customer(FinCustomer|ModelInterface|array|string $values): FinCustomer
    {
        $attributes = $this->sanitize($values, [
            'id' => ['required', 'string', 'max:1,60'],
            'name' => ['nullable', 'string', 'between:1,384'],
            'email' => ['nullable', 'string', 'between:1,254'],
        ]);

        return tap(FinCustomer::findOrnew($attributes->input('id')))
            ->forceFill([
                'id' => $attributes->input('id'),
                'name' => $attributes->input('name'),
                'email' => $attributes->input('email'),
                'phone_cc' => $attributes->input('phone_cc'),
                'phone_no' => $attributes->input('phone_no'),
                'addr_country' => $attributes->input('addr_country'),
                'addr_state' => $attributes->input('addr_state'),
                'addr_city' => $attributes->input('addr_city'),
                'addr_line_1' => $attributes->input('addr_line_1'),
                'addr_line_2' => $attributes->input('addr_line_2'),
                'addr_line_3' => $attributes->input('addr_line_3'),
                'addr_post_code' => $attributes->input('addr_post_code'),
                'card_registration' => $attributes->input('card_registration'),
                'directdebit_registration' => $attributes->input('directdebit_registration'),
                'created' => $attributes->input('created') ?? $model->created ?? now(),
                'updated' => $attributes->input('updated'),
            ]);
    }

    /**
     * 値をFinShopモデルに結合する
     *
     * @throws ValidationException
     */
    public function shop(FinPlatform|ModelInterface|array|string $values): FinPlatform
    {
        $attributes = $this->sanitize($values, [
            'id' => ['required', 'string', 'size:13'],
            'shop_name' => ['required', 'string', 'max:20'],
            'shop_type' => ['nullable', new Enum(ShopType::class)],
            'platform_id' => ['required', 'string', 'size:13'],
            'platform_name' => ['required', 'string', 'max:50'],
            'shared_customer_flag' => ['nullable', 'boolean'],
            'api_key_display_flag' => ['nullable', 'boolean'],
            'platform_rate_list' => ['nullable', 'array'],
        ]);

        /*
         * モデルを取得、または新規作成
         */
        return tap(FinPlatform::findOrnew($attributes->input('id')))
            ->forceFill([
                'id' => $attributes->input('id'),
                'shop_name' => $attributes->input('shop_name'),
                'shop_type' => $attributes->input('shop_type'),
                'platform_id' => $attributes->input('platform_id'),
                'platform_name' => $attributes->input('platform_name'),
                'shared_customer_flag' => $attributes->boolean('shared_customer_flag'),
                'customer_group_id' => $attributes->input('customer_group_id'),
                'platform_rate_list' => $attributes->input('platform_rate_list'),
                'send_mail_address' => $attributes->input('send_mail_address'),
                'shop_email_address' => $attributes->input('shop_email_address'),
                'log_keep_days' => $attributes->input('log_keep_days'),
                'api_version' => $attributes->input('api_version'),
                'api_key_display_flag' => $attributes->boolean('api_key_display_flag'),
                'created' => $attributes->input('created') ?? $model->created ?? now(),
                'updated' => $attributes->input('updated'),
            ]);
    }

    /**
     * 値をFinPaymentに結合する
     */
    public function payment(FinPayment|ModelInterface|array|string $values): FinPayment
    {
        $attributes = $this->sanitize($values, [
            'shop_id' => ['required', 'string', 'size:13'],
            'id' => ['required', 'string', 'between:1,30'],
            'access_id' => ['required', 'string', 'between:1,24'],
            'amount' => ['required', 'integer'],
            'tax' => ['required', 'integer'],
            'total_amount' => ['required', 'integer'],
            'pay_type' => ['required', new Enum(PayType::class)],
        ]);

        $id = $attributes->input('id');

        $pay_method = match ($attributes->enum('pay_type', PayType::class)) {
            PayType::CARD => $this->paymentCard($id, $attributes->toArray()),
            PayType::KONBINI => $this->paymentKonbini($id, $attributes->toArray()),
            PayType::APPLEPAY => $this->paymentApplePay($id, $attributes->toArray()),
            PayType::PAYPAY => $this->paymentPayPay($id, $attributes->toArray()),
            default => null,
        };

        /*
         * モデルを取得、または新規作成
         */
        return tap(FinPayment::findOrnew($attributes->input('id')), function (FinPayment $model) use ($attributes) {
            $pay_method = match ($attributes->enum('pay_type', PayType::class)) {
                PayType::CARD => $this->paymentCard($model, $attributes->toArray()),
                PayType::KONBINI => $this->paymentKonbini($model, $attributes->toArray()),
                PayType::APPLEPAY => $this->paymentApplePay($model, $attributes->toArray()),
                PayType::PAYPAY => $this->paymentPayPay($model, $attributes->toArray()),
                default => null,
            };

            $model->pay_method()->associate($pay_method);
        })->forceFill([
            'id' => $id,
            'shop_id' => $attributes->input('shop_id'),
            'pay_type' => $attributes->input('pay_type'),
            'job_code' => $attributes->input('job_code'),
            'status' => $attributes->input('status'),
            'access_id' => $attributes->input('access_id'),
            'amount' => $attributes->input('amount'),
            'tax' => $attributes->input('tax'),
            'total_amount' => $attributes->input('total_amount'),
            'client_field_1' => $attributes->input('client_field_1'),
            'client_field_2' => $attributes->input('client_field_2'),
            'client_field_3' => $attributes->input('client_field_3'),
            'processed_at' => $attributes->input('processed_at'),
            'customer_id' => $attributes->input('customer_id'),
            'customer_group_id' => $attributes->input('customer_group_id'),
            'error_code' => $attributes->input('error_code'),
            'created' => $attributes->input('created') ?? $model->created ?? now(),
            'updated' => $attributes->input('updated'),
        ]);
    }

    public function paymentCard(FinPayment $payment, Arrayable|array $values): FinPaymentCard
    {
        $attributes = $this->sanitize($values, [
            'card_id' => ['required', 'string', 'size:25'],
            'card_no' => ['required', 'string', 'max:16'],
            'expire' => ['required', 'string', 'size:4'],
            'holder_name' => ['required', 'string', 'max:50'],
            'type' => ['required', new Enum(CardBrand::class)],
            'card_no_hash' => ['required', 'string', 'size:64'],
        ]);

        return tap($payment->pay_method instanceof FinPaymentCard ? $payment->pay_method : new FinPaymentCard, function (FinPaymentCard $model) use ($attributes) {
            $model->forceFill([
                'card_id' => $attributes->input('card_id'),
                'brand' => $attributes->input('brand'),
                'card_no' => $attributes->input('card_no'),
                'expire' => $attributes->input('expire'),
                'holder_name' => $attributes->input('holder_name'),
                'card_no_hash' => $attributes->input('card_no_hash'),
                'method' => $attributes->input('method'),
                'pay_times' => $attributes->input('pay_times'),
                'bulk_payment_id' => $attributes->input('bulk_payment_id'),
                'sub_payment_id' => $attributes->input('sub_payment_id'),
                'tds_type' => $attributes->input('tds_type'),
                'tds2_type' => $attributes->input('tds2_type'),
                'tds2_ret_url' => $attributes->input('tds2_ret_url'),
                'return_url' => $attributes->input('return_url'),
                'return_url_on_failure' => $attributes->input('return_url_on_failure'),
                'return_url_on_timeout' => $attributes->input('return_url_on_timeout'),
                'tds2_status' => $attributes->input('tds2_status'),
                'merchant_name' => $attributes->input('merchant_name'),
                'forward' => $attributes->input('forward'),
                'issuer' => $attributes->input('issuer'),
                'transaction_id' => $attributes->input('transaction_id'),
                'approve' => $attributes->input('approve'),
                'auth_max_date' => $attributes->input('auth_max_date'),
                'created' => $attributes->input('created') ?? $model->created ?? now(),
                'updated' => $attributes->input('updated'),
            ]);
        });
    }

    public function paymentPayPay(string $payment_id, Arrayable|array $values) {}

    public function paymentApplePay(string $payment_id, Arrayable|array $values): FinPaymentApplePay {}

    public function paymentKonbini(string $payment_id, Arrayable|array $values): FinPaymentKonbini {}

    /**
     * 引き受けた値を配列に同一して返却する
     *
     * @throws ValidationException
     */
    private function sanitize(Arrayable|JsonSerializable|array|string $values, array $rules = []): ValidatedInput
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        // ModelInterface の場合は配列に変換
        if ($values instanceof JsonSerializable) {
            $values = $values->jsonSerialize();
        }

        // 文字列の場合はJSONと見做して、配列にパースする
        if (is_string($values)) {
            $values = json_decode($values, true, 32);
        }

        // オブジェクト型の場合は配列化する
        if (is_object($values)) {
            $values = get_object_vars($values);
        }

        // キー名をスネーク式に変更しつつ値を返却する
        return Validator::make(
            Arr::mapWithKeys($values, fn ($value, $key) => [Str::snake($key) => $value]),
            $rules
        )->safe();
    }

    /**
     * モデルオブジェクトを配列に変換する
     *
     * @param  Arrayable|JsonSerializable|array  $model  元となるオブジェクト
     * @param  bool  $camel  trueの場合、キーをCamelケースで返却。falseの場合はオリジナルママです
     */
    public function castArray(Arrayable|JsonSerializable|array $model, bool $camel = true): array
    {
        if ($model instanceof JsonSerializable) {
            $model = $model->jsonSerialize();
        } elseif ($model instanceof Arrayable) {
            $model = $model->toArray();
        } elseif (is_object($model)) {
            $model = get_object_vars($model);
        }

        $model = is_array($model) ? $model : [];

        return Arr::mapWithKeys($model, fn ($value, $key) => [$camel ? Str::camel($key) : $key => $value]);
    }
}
