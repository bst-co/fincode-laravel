<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\OpenAPI\Model\ExaminationMasterId;
use Fincode\OpenAPI\Model\PlatformRateConfig;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class PlatformRateObject implements Arrayable, JsonSerializable
{
    public function __construct(
        public ExaminationMasterId $id,
        public ?float $platform_rate = null,
        public ?int $fixed_fee = null,
        public ?float $web_registration_fee = null,
        public ?int $paypay_content_category_type = null,
    ) {}

    public static function make(array|PlatformRateConfig $value): PlatformRateObject
    {
        if ($value instanceof PlatformRateConfig) {
            return new self(
                $value->getId(),
                $value->getPlatformRate(),
                $value->getFixedFee(),
                $value->getWebRegistrationFee(),
                $value->getPaypayContentCategoryType(),
            );
        }

        $value = is_object($value) ? get_object_vars($value) : $value;

        return new self(
            $value['id'],
            $value['platform_rate'],
            $value['fixed_fee'],
            $value['web_registration_fee'],
            $value['paypay_content_category_type'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->value,
            'platform_rate' => $this->platform_rate,
            'fixed_fee' => $this->fixed_fee,
            'web_registration_fee' => $this->web_registration_fee,
            'paypay_content_category_type' => $this->paypay_content_category_type,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
