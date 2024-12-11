<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use OpenAPI\Fincode\Model\ShopType;

class FinPlatformFactory extends Factory
{
    use HasFactoryExtend;

    /**
     * {@inheritdoc}
     */
    protected $model = FinPlatform::class;

    public function definition(): array
    {
        return [
            'shop_id' => $this->getIdentify(13, 's_'),
            'shop_name' => Str::substr($this->faker->company(), 0, 20),
            'shop_type' => $this->faker->randomElement(ShopType::cases()),
            'platform_id' => $this->getIdentify(13, 'p_'),
            'platform_name' => Str::substr($this->faker->company(), 0, 50),
            'shared_customer_flag' => $this->faker->boolean(),
            'customer_group_id' => $this->getIdentify(13, 'cg_'),
            'platform_rate_list' => [],
            'send_mail_address' => $this->faker->email(),
            'shop_mail_address' => $this->faker->email(),
            'log_keep_days' => $this->faker->randomNumber(),
            'api_version' => $this->faker->semver(),
            'api_key_display_flag' => $this->faker->boolean(),
            'created' => static::$created ??= \Carbon\Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
