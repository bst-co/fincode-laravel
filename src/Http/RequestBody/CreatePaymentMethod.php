<?php

namespace Fincode\Laravel\Http\RequestBody;

use ErrorException;
use Fincode\Laravel\Models\FinPayment;
use Illuminate\Contracts\Support\Arrayable;
use OpenAPI\Fincode\Model\ModelInterface;
use OpenAPI\Fincode\Model\PayType;

/**
 * @template TInterface of ModelInterface
 */
class CreatePaymentMethod implements Arrayable
{
    /**
     * @param  CreatePayment  $instance  親オブジェクト
     * @param  class-string<TInterface>  $interface  リクエストオブジェクト
     */
    public function __construct(
        protected readonly CreatePayment $instance,
        protected readonly string $interface,
        protected readonly PayType $pay_type,
        protected readonly array $attributes = [],
    ) {}

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

    /**
     * @return void
     *
     * @throws ErrorException
     */
    final public function exec(ModelInterface|array|null $model = null): FinPayment
    {
        return $this->instance->exec($this->marshal($model));
    }

    /**
     * APIのリクエストに必要となるModelInterfaceを生成する
     *
     * @throws ErrorException
     */
    final protected function marshal(ModelInterface|array $model = []): ?ModelInterface
    {
        if (class_exists($this->interface)) {
            if (is_a($this->instance, ModelInterface::class, true)) {
                $binding = $this->instance->binding;

                return new $this->interface([
                    ...$binding->castArray($model),
                    ...$binding->castArray($this->toArray()),
                ]);
            }

            throw new ErrorException('ModelInterface not found: '.$this->interface.'.');
        }

        throw new ErrorException('Class not found: '.$this->interface.'.');
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
            ...$this->attributes,
        ];
    }
}
