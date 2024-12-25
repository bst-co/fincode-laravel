<?php

namespace Fincode\Laravel\Clients;

use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Models\FinCustomer;

abstract class FincodeCustomerAbstract extends FincodeAbstract
{
    protected readonly FinCustomer $customer;

    /**
     * @throws FincodeRequestException
     */
    public function __construct(
        FinCustomer|string $customer,
        ?FincodeRequestToken $token = null,
    ) {
        parent::__construct($token);
        $this->customer = $customer instanceof FinCustomer ? $customer : FinCustomer::findOrFail($customer);
    }

    /**
     * @throws FincodeRequestException
     */
    public static function make(
        FinCustomer $customer,
        ?FincodeRequestToken $token = null,
    ): static {
        return new static($customer, $token);
    }
}
