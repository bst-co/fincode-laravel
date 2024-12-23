<?php

namespace Fincode\Laravel\Database\Factories;

use Fincode\Laravel\Concerns\HasFactoryExtend;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\OpenAPI\Model\CardBrand;
use Fincode\OpenAPI\Model\CardPayMethod;
use Fincode\OpenAPI\Model\CardPayTimes;
use Fincode\OpenAPI\Model\Tds2Type;
use Fincode\OpenAPI\Model\TdsType;
use Fincode\OpenAPI\Model\ThreeDSecure2Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FinPaymentCardFactory extends Factory
{
    use HasFactoryExtend;

    protected $model = FinPaymentCard::class;

    public function definition(): array
    {
        return [
            'card_id' => $this->getIdentify(25, 'c_'),
            'brand' => $this->faker->randomElement(CardBrand::cases()),
            'card_no' => $this->faker->numerify('***********####'),
            'expire' => $this->faker->date(),
            'holder_name' => $this->faker->name(),
            'card_no_hash' => Str::substr($this->faker->sha256(), 1, 64),
            'method' => $this->faker->randomElement(CardPayMethod::cases()),
            'pay_times' => $this->faker->randomElement(CardPayTimes::cases()),
            'bulk_payment_id' => $this->getIdentify(25),
            'subscription_id' => $this->getIdentify(25, 'su_'),
            'tds_type' => $this->faker->randomElement(TdsType::cases()),
            'tds2_type' => $this->faker->randomElement(Tds2Type::cases()),
            'tds2_ret_url' => $this->faker->url(),
            'return_url' => $this->faker->url(),
            'return_url_on_failure' => $this->faker->url(),
            'tds2_status' => $this->faker->randomElement(ThreeDSecure2Status::cases()),
            'merchant_name' => $this->faker->name(),
            'forward' => $this->faker->bothify('???????'),
            'issuer' => $this->faker->bothify('???????'),
            'transaction_id' => $this->getIdentify(28),
            'approve' => $this->faker->bothify('???????'),
            'auth_max_date' => Carbon::now(),
            'item_code' => $this->faker->bothify('#######'),
            'created' => static::$created ??= \Carbon\Carbon::make('2000-01-01 00:00:00'),
            'updated' => Carbon::now(),
        ];
    }
}
