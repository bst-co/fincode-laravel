<?php

namespace Fincode\Laravel\Http\RequestBody;

use ErrorException;
use Fincode\Laravel\Models\FinPayment;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PayType;

/**
 * @template TInterface of ModelInterface
 *
 * @extends PaymentMethod<TInterface>
 */
class CreatePaymentMethod extends PaymentMethod
{
    /**
     * @param  PayType  $pay_type  決済種別
     */
    public function __construct(
        CreatePayment $instance,
        string $interface,
        protected readonly PayType $pay_type,
        array $attributes = [],
    ) {
        parent::__construct($instance, $interface, $attributes);
    }

    protected ?string $client_field_2 = null;

    /**
     * 加盟店自由項目 2
     *
     * @return $this
     */
    public function setClientField2(string $client_field): static
    {
        $this->client_field_2 = $client_field;

        return $this;
    }

    /**s
     * APIリクエストを中継する
     * @throws ErrorException
     */
    final public function exec(ModelInterface|array|null $model = null): FinPayment
    {
        return $this->instance->exec($this->marshal($model));
    }

    /**
     * 値を指定のインターフェイスに注入する
     */
    protected function marshalValue(ModelInterface|array $model = []): array
    {
        $binding = $this->instance->binding;
        $token = $this->instance->token;

        return [
            ...parent::marshalValue($model),
            ...$binding->castArray([
                'td_tenant_name' => $token->tenant_name,
                'client_field_1' => $token->client_field_1,
                'client_field_3' => $token->client_field_3,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'id' => null,
            'pay_type' => $this->pay_type->value,
            'amount' => (string) max(0, $this->instance->amount),
            'tax' => (string) max($this->instance->tax),
            'client_field_2' => $this->client_field_2,
            ...parent::toArray(),
        ];
    }
}
