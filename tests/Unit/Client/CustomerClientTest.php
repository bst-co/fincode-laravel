<?php

namespace Fincode\Laravel\Test\Unit\Client;

use Fincode\Laravel\Clients\FincodeCustomerRequest;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Test\TestCase;

class CustomerClientTest extends TestCase
{
    public function test_retrieve()
    {
        dump(env('FINCODE_API_KEY'));
        $client = new FincodeCustomerRequest;

        $response = $client->retrieve('fin_test_1111');

        $this->assertInstanceOf(FinCustomer::class, $response);
    }
}