<?php

namespace Fincode\Laravel\Database\Factories;

use Carbon\Carbon;
use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use OpenAPI\Fincode\Model\IntervalCount;
use OpenAPI\Fincode\Model\IntervalPattern;

class FinPlanFactory extends Factory
{
    use HasFactoryExtend;

    /**
     * {@inheritdoc}
     */
    protected $model = FinPlan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id' => $this->getIdentify(25, 'pl_'),
            'plan_name' => $this->faker->name(),
            'description' => $this->faker->text(400),
            'shop_id' => $this->getIdentify(13, 's_'),
            'amount' => $this->faker->randomNumber(),
            'tax' => $this->faker->randomNumber(),
            'total' => $this->faker->randomNumber(),
            'interval_pattern' => $this->faker->randomElement(IntervalPattern::cases()),
            'interval_count' => $this->faker->randomNumber(IntervalCount::cases()),
            'used_flag' => $this->faker->boolean(),
            'delete_flag' => $this->faker->boolean(),
            'created' => static::$created ??= \Carbon\Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
