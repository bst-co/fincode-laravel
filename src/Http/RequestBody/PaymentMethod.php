<?php

namespace Fincode\Laravel\Http\RequestBody;

use ErrorException;
use Illuminate\Contracts\Support\Arrayable;
use OpenAPI\Fincode\Model\ModelInterface;

/**
 * @template TInterface of ModelInterface
 * @template TInstance of PaymentBase
 */
class PaymentMethod implements Arrayable
{
    /**
     * @param  TInstance  $instance  親オブジェクト
     * @param  class-string<TInterface>  $interface  リクエストオブジェクト
     * @param  array  $attributes  追加パラメータ
     */
    public function __construct(
        protected readonly PaymentBase $instance,
        protected readonly string $interface,
        protected readonly array $attributes = [],
    ) {}

    /**
     * APIのリクエストに必要となるModelInterfaceを生成する
     *
     * @throws ErrorException
     */
    final protected function marshal(ModelInterface|array $model = []): ?ModelInterface
    {
        if (class_exists($this->interface)) {
            if (is_a($this->instance, ModelInterface::class, true)) {

                return new $this->interface($this->marshalValue($model));
            }

            throw new ErrorException('ModelInterface not found: '.$this->interface.'.');
        }

        throw new ErrorException('Class not found: '.$this->interface.'.');
    }

    protected function marshalValue(ModelInterface|array $model = []): array
    {
        $binding = $this->instance->binding;

        return [
            ...$binding->castArray($model),
            ...$binding->castArray($this->toArray()),
        ];
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
