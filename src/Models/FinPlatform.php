<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Fincode\Laravel\Database\Factories\FinPlatformFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPlatform extends Model
{
    use HasFactory, HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

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
    protected static function booted(): void
    {
        static::duplicates(['shop_id', 'shop_type'], ['updated']);
    }

    /**
     * 同一のShopIDを有する、兄弟要素を取得する
     */
    public function siblings(): HasMany|FinPlatform
    {
        return $this->hasMany(FinPlatform::class, 'shop_id', 'shop_id')->latest('updated');
    }
}
