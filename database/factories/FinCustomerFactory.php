<?php

namespace Fincode\Laravel\Database\Factories;

use Carbon\Carbon;
use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinCustomerFactory extends Factory
{
    use HasFactoryExtend;

    /**
     * {@inheritdoc}
     */
    protected $model = FinCustomer::class;

    public function definition(): array
    {
        return [
            'id' => $this->getIdentify(60, 'cs_'),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_cc' => $this->faker->numberBetween(1, 999),
            'phone_no' => $this->faker->imei(),
            'addr_country' => $this->faker->countryISOAlpha3(),
            'addr_state' => $this->faker->numberBetween(1, 999),
            'addr_city' => $this->faker->city(),
            'addr_line_1' => $this->faker->streetName(),
            'addr_line_2' => $this->faker->buildingNumber(),
            'addr_line_3' => '',
            'addr_post_code' => $this->faker->postcode(),
            'card_registration' => $this->faker->boolean(),
            'directdebit_registration' => $this->faker->boolean(),
            'created' => static::$created ??= Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
