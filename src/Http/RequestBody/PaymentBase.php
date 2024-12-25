<?php

namespace Fincode\Laravel\Http\RequestBody;

use Fincode\Laravel\Clients\FincodeRequestToken;
use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\OpenAPI\Model\ModelInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

abstract class PaymentBase implements Arrayable, JsonSerializable
{
    public readonly FinModelBinding $binding;

    public function __construct(
        public readonly FincodeRequestToken $token,
    ) {
        $this->binding = new FinModelBinding;
    }

    /**
     * @template TModel of Model
     *
     * @return TModel
     */
    abstract public function exec(ModelInterface $body): Model;

    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
