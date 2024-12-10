<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFincode\Concerns\HasMilliDateTime;
use LaravelFincode\Concerns\HasRejectDuplicates;
use OpenAPI\Fincode;

class FinPlatform extends Model
{
    use HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'shop_type' => Fincode\Model\ShopType::class,
        'shared_customer_flag' => 'boolean',
        'api_key_display_flag' => 'boolean',
        'platform_rate_list' => 'array',
        'expire_to' => 'datetime',
        'expired_at' => 'datetime',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['shop_id', 'shop_type', 'updates']);
    }

    /**
     * 同一のShopIDを有する、兄弟要素を取得する
     */
    public function siblings(): HasMany|FinPlatform
    {
        return $this->hasMany(FinPlatform::class, 'shop_id', 'shop_id')->latest('updated');
    }

    /**
     * 使用可能なモデルを取得する
     *
     * @noinspection PhpUnused
     */
    protected function scopeWhereValidity(Builder|FinPlatform $query): Builder|FinPlatform
    {
        return $query->whereNotNull('expired_at');
    }
}
