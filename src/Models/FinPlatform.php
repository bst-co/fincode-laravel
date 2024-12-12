<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinPlatformFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;
use OpenAPI\Fincode\Model\ShopType;

/**
 * @property-read Collection<FinPlatform>|null $siblings
 */
class FinPlatform extends Model
{
    use HasFactory, HasHistories, HasMilliDateTime, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'shop_type' => Fincode\Model\ShopType::class,
        'shared_customer_flag' => 'boolean',
        'api_key_display_flag' => 'boolean',
        'platform_rate_list' => 'array',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinPlatformFactory
    {
        return new FinPlatformFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void {}

    /**
     * ショップの通信トークンを取得する
     */
    public function token(): HasOne|FinPlatformToken
    {
        return $this->hasOne(FinPlatformToken::class, 'id', 'id');
    }

    /**
     * 顧客情報を共有しないプラットフォームであるかを判定する
     */
    protected function isPrivateShop(): Attribute
    {
        return Attribute::make(
            fn () => ! $this->shared_customer_flag && $this->shop_type === ShopType::PLATFORM,
        );
    }
}
