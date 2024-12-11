<?php

namespace Fincode\Laravel\Database\Factories;

use Carbon\Carbon;
use DateTimeInterface;
use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use OpenAPI\Fincode\Model\CardBrand;

/**
 * @extends Factory<FinCard>
 */
class FinCardFactory extends Factory
{
    use HasFactoryExtend;

    /**
     * {@inheritdoc}
     */
    protected $model = FinCard::class;

    public function definition(): array
    {
        return [
            'card_id' => $this->getIdentify(25, 'c_'),
            'customer_id' => $this->getIdentify(26, 'cs_'),
            'default_flag' => $this->faker->boolean,
            'card_no' => $this->faker->creditCardNumber,
            'expire' => $this->faker->date(),
            'holder_name' => $this->faker->name(),
            'brand' => $this->faker->randomElement(CardBrand::cases()),
            'card_no_hash' => Str::substr($this->faker->sha256(), 1, 64),
            'created' => static::$created ??= Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
