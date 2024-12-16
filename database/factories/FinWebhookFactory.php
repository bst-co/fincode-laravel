<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use OpenAPI\Fincode\Model\FincodeEvent;

class FinWebhookFactory extends Factory
{
    use HasFactoryExtend;

    protected $model = FinWebhook::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $hash = $this->faker->uuid();
        $event = $this->faker->randomElement(FincodeEvent::cases());

        return [
            'id' => $this->getIdentify(50, 'w_'),
            'url' => route('fincode.webhook', ['hash' => $hash, 'event' => $event->value]),
            'hash' => $hash,
            'event' => $event,
            'signature' => $this->faker->sha256(),
            'created' => static::$created ??= Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
