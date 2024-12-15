<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use OpenAPI\Fincode\Model\PayType;
use OpenAPI\Fincode\Model\SubscriptionStatus;

class FinSubscriptionFactory extends Factory
{
    use HasFactoryExtend;

    /**
     * {@inheritdoc}
     */
    protected $model = FinSubscription::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => $this->getIdentify(25, 'su_'),
            'shop_id' => $this->getIdentify(13, 's_'),
            'pay_type' => $this->faker->randomElement([PayType::CARD, PayType::DIRECTDEBIT]),
            'plan_id' => $this->getIdentify(25, 'pl_'),
            'plan_name' => $this->faker->text(),
            'customer_id' => $this->getIdentify(60, 'c_'),
            'card_id' => $this->getIdentify(25, 'cs_'),
            'payment_method_id' => $this->getIdentify(25, 'pm_'),
            'amount' => $this->faker->randomNumber(),
            'tax' => $this->faker->randomNumber(),
            'total_amount' => $this->faker->randomNumber(),
            'initial_amount' => $this->faker->randomNumber(),
            'initial_tax' => $this->faker->randomNumber(),
            'initial_total_amount' => $this->faker->randomNumber(),
            'status' => $this->faker->randomElement(SubscriptionStatus::cases()),
            'start_date' => Carbon::now(),
            'next_charge_date' => Carbon::now(),
            'stop_date' => Carbon::now(),
            'end_month_flag' => $this->faker->boolean(),
            'error_code' => $this->faker->text(11),
            'client_field_1' => $this->faker->text(100),
            'client_field_2' => $this->faker->text(100),
            'client_field_3' => $this->faker->text(100),
            'remarks' => $this->faker->text(9),
            'created' => static::$created ??= \Carbon\Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
