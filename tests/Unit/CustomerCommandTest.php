<?php

namespace Fincode\Laravel\Test\Unit;

use Fincode\Laravel\Test\TestCase;

class CustomerCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_token() {}

    public function test_customer_retrieve()
    {
        $result = $this->artisan('fincode:customer:retrieve', [
            'customer_id' => 'fin_test_1111',
            '-vvv' => true,
        ])
            ->assertOk()
            ->run();
    }
}
