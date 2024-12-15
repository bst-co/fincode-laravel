<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinShopToken;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinShopTokenFactory extends Factory
{
    use HasFactoryExtend;

    protected $model = FinShopToken::class;

    public function definition(): array
    {
        return [
            'id' => $this->getIdentify(13, 's_'),
            'tenant_name' => $this->faker->name(),
            'public_key' => $this->faker->md5(),
            'secret_key' => $this->faker->md5(),
            'client_field' => $this->faker->word(),
        ];
    }
}
