<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinShop;
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
        $event = $this->faker->randomElement(FincodeEvent::cases());
        $shop_id = $this->getIdentify(13, 's_');

        return [
            'id' => $this->getIdentify(50, 'w_'),
            'shop_id' => $this->getIdentify(13, 's_'),
            'url' => route('fincode.webhook', ['shop' => $shop_id, 'event' => $event->value]),
            'event' => $event,
            'signature' => $this->faker->uuid(),
            'created' => static::$created ??= Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }

    /**
     * イベント種の上書き
     */
    public function event(FincodeEvent|string $event): FinWebhookFactory
    {
        $event = $event?->value ?? $event;

        return $this->state(function (array $attributes) use ($event) {
            return [
                'event' => $event,
                'url' => route('fincode.webhook', ['shop' => $attributes['shop_id'], 'event' => $event]),
            ];
        });
    }

    /**
     * ショップ情報を上書き
     */
    public function shop(FinShop|string $shop): FinWebhookFactory
    {
        $shop_id = $shop?->id ?? $shop;

        return $this->state(function (array $attributes) use ($shop_id) {
            return [
                'shop_id' => $shop_id,
                'url' => route('fincode.webhook', ['shop' => $shop_id, 'event' => $attributes['event']?->value ?? $attributes['event']]),
            ];
        });
    }
}
