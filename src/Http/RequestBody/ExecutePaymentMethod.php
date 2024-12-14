<?php

namespace Fincode\Laravel\Http\RequestBody;

class ExecutePaymentMethod extends PaymentMethod
{
    public function __construct(
        PaymentBase $instance,
        string $interface,
        array $attributes = []
    ) {
        parent::__construct($instance, $interface, $attributes);
    }
}
