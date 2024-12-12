<?php

namespace Fincode\Laravel\Eloquent;

use OpenAPI\Fincode\Model\ExaminationMasterId;
use OpenAPI\Fincode\Model\PlatformRateConfig;

class PlatformRateObject
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
            $value['get_fixed_fee'],
            $value['get_web_registration_fee'],
            $value['get_paypay_content_category_type'],
        );
    }
}
