<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinPayment;
use Fincode\OpenAPI\Model\CardPaymentJobCode;
use Fincode\OpenAPI\Model\PaymentStatus;
use Fincode\OpenAPI\Model\PayType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FinPaymentFactory extends Factory
{
    use HasFactoryExtend;

    protected $model = FinPayment::class;

    public function definition(): array
    {
        return [
            'id' => $this->getIdentify(30, 'o_'),
            'shop_id' => $this->getIdentify(13, 's_'),
            'pay_type' => $this->faker->randomElement(PayType::cases()),
            'job_code' => $this->faker->randomElement(CardPaymentJobCode::cases()),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'access_id' => $this->getIdentify(24, 'a_'),
            'amount' => $this->faker->randomNumber(),
            'tax' => $this->faker->randomNumber(),
            'total_amount' => $this->faker->randomNumber(),
            'client_field_1' => $this->faker->word(),
            'client_field_2' => $this->faker->word(),
            'client_field_3' => $this->faker->word(),
            'process_date' => Carbon::now(),
            'customer_id' => $this->getIdentify(60, 'c_'),
            'customer_group_id' => $this->getIdentify(13, 'cg_'),
            'error_code' => null,
            'created' => static::$created ??= \Carbon\Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
