<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\ValidatedInput;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use JsonSerializable;
use OpenAPI\Fincode\Model\CardBrand;
use OpenAPI\Fincode\Model\CardPaymentJobCode;
use OpenAPI\Fincode\Model\CardPayMethod;
use OpenAPI\Fincode\Model\FincodeEvent;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PaymentStatus;
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
            'default_flag' => ['required', 'boolean'],
            'card_no' => ['required', 'string', 'max:16'],
            'holder_name' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', new Enum(CardBrand::class)],
            'card_no_hash' => ['required', 'string', 'max:64'],
        ]);

        /*
         * モデルを取得、または新規作成
         */
        return tap(
            FinCard::findOrnew($attributes->input('id')),
            function (FinCard $model) use ($attributes) {
                $model
                    ->forceFill([
                        'id' => $attributes->input('id'),
                        ...$this->concat($model, $attributes),
                    ]);
            }
        );
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

        return tap(
            FinCustomer::findOrnew($attributes->input('id')),
            function (FinCustomer $model) use ($attributes) {
                $model
                    ->forceFill([
                        'id' => $attributes->input('id'),
                        ...$this->concat($model, $attributes),
                    ]);
            });
    }

    /**
     * 値をFinShopモデルに結合する
     *
     * @throws ValidationException
     */
    public function shop(FinShop|ModelInterface|array|string $values): FinShop
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
        return tap(
            FinShop::findOrnew($attributes->input('id')),
            function (FinShop $model) use ($attributes) {
                $model
                    ->forceFill([
                        'id' => $attributes->input('id'),
                        ...$this->concat($model, $attributes),
                    ]);
            }
        );
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
            'status' => ['required', new Enum(PaymentStatus::class)],
            'pay_type' => ['required', new Enum(PayType::class)],
        ]);

        $id = $attributes->input('id');

        /*
         * モデルを取得、または新規作成
         */
        return tap(
            FinPayment::findOrnew($attributes->input('id')),
            function (FinPayment $model) use ($attributes, $id) {
                $pay_method = match ($attributes->enum('pay_type', PayType::class)) {
                    PayType::CARD => $this->paymentCard($model, $attributes->toArray()),
                    PayType::KONBINI => $this->paymentKonbini($model, $attributes->toArray()),
                    PayType::APPLEPAY => $this->paymentApplePay($model, $attributes->toArray()),
                    PayType::PAYPAY => $this->paymentPayPay($model, $attributes->toArray()),
                    default => null,
                };

                $model
                    ->forceFill([
                        'id' => $id,
                        ...$this->concat($model, $attributes),
                    ])
                    ->pay_method()->associate($pay_method);

                dump($model->toArray());
            });
    }

    /**
     * クレジットカード決済情報を結合する
     */
    public function paymentCard(FinPayment $payment, Arrayable|array $values): FinPaymentCard
    {
        $attributes = $this->sanitize($values, [
            'card_id' => ['nullable', 'string', 'size:25'],
            'card_no' => ['nullable', 'string', 'max:16'],
            'expire' => ['nullable', 'string', 'size:4'],
            'holder_name' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', new Enum(CardBrand::class)],
            'job_code' => ['required', new Enum(CardPaymentJobCode::class)],
            'card_no_hash' => ['nullable', 'string', 'size:64'],
            'method' => ['required', new Enum(CardPayMethod::class)],
        ]);

        return tap(
            $payment->getPayMethodBy(FinPaymentCard::class) ?? new FinPaymentCard,
            fn (FinPaymentCard $model) => $model->fill($this->concat($model, $attributes))
        );
    }

    /**
     * PayPay決済情報を結合する
     */
    public function paymentPayPay(FinPayment $payment, Arrayable|array $values) {}

    /**
     * ApplePay決済情報を結合する
     */
    public function paymentApplePay(FinPayment $payment, Arrayable|array $values): FinPaymentApplePay
    {
        $attributes = $this->sanitize($values, [
            'card_id' => ['required', 'string', 'size:25'],
            'card_no' => ['required', 'string', 'max:16'],
            'expire' => ['required', 'string', 'size:4'],
            'holder_name' => ['required', 'string', 'max:50'],
            'type' => ['required', new Enum(CardBrand::class)],
            'card_no_hash' => ['required', 'string', 'size:64'],
        ]);

        return tap(
            $payment->getPayMethodBy(FinPaymentApplePay::class) ?? new FinPaymentApplePay,
            fn (FinPaymentApplePay $model) => $model->fill($this->concat($model, $attributes))
        );
    }

    /**
     * コンビニ決済情報を結合する
     */
    public function paymentKonbini(FinPayment $payment, Arrayable|array $values): FinPaymentKonbini
    {
        $attributes = $this->sanitize($values, [
        ]);

        return tap(
            $payment->getPayMethodBy(FinPaymentKonbini::class) ?? new FinPaymentKonbini,
            fn (FinPaymentKonbini $model) => $model->fill($this->concat($model, $attributes))
        );
    }

    public function webhook(FinWebhook|ModelInterface|array|string $values, FinShop|string $shop): FinWebhook
    {
        $shop_id = $shop instanceof FinShop ? $shop->id : $shop;

        $attributes = $this->sanitize($values, [
            'id' => ['required', 'string', 'max:1,50'],
            'url' => ['required', 'string'],
            'event' => ['required', new Enum(FincodeEvent::class)],
            'signature' => ['required', 'string', 'max:60'],
            'created' => ['required', 'date'],
            'updated' => ['nullable', 'date'],
        ]);

        return tap(
            FinWebhook::findOrnew($attributes->input('id')),
            fn (FinWebhook $model) => $model
                ->forceFill([
                    'id' => $attributes->input('id'),
                    'shop_id' => $shop_id,
                    ...$this->concat($model, $attributes),
                ])
        );
    }

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

        // キー名をスネーク型に変更
        $values = Arr::mapWithKeys($values, fn ($value, $key) => [Str::snake($key) => $value]);

        // Validationを実行、エラーがある場合は例外
        Validator::make($values, $rules)->validated();

        // 値のコンテナを返却する
        return new ValidatedInput($values);
    }

    private function concat(Model $model, ValidatedInput $input): array
    {
        $columns = $model->getFillable();

        $values = [];

        foreach ($columns as $column) {
            if ($input->has($column)) {
                $values[$column] = $input->input($column);
            } else {
                $values[$column] = $model->getAttribute($column);
            }
        }

        return $values;
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
