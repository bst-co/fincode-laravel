<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinShopFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;
use OpenAPI\Fincode\Model\ShopType;

class FinShop extends Model
{
    use HasFactory, HasHistories, HasMilliDateTime, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'shop_name',
        'shop_type',
        'platform_id',
        'platform_name',
        'shared_customer_flag',
        'customer_group_id',
        'platform_rate_list',
        'send_mail_address',
        'shop_mail_address',
        'log_keep_days',
        'api_version',
        'api_key_display_flag',
        'created',
        'updated',
    ];

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
    protected static function newFactory(): FinShopFactory
    {
        return new FinShopFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void {}

    /**
     * ショップの通信トークンを取得する
     */
    public function token(): HasOne|FinShopToken
    {
        return $this->hasOne(FinShopToken::class, 'id', 'id');
    }

    /**
     * Webhookエンドポイント情報と連携
     */
    public function webhooks(): HasMany|FinWebhook
    {
        return $this->hasMany(FinWebhook::class, 'shop_id', 'id');
    }

    /**
     * サブスクリプションプランへの連携
     */
    public function plans(): HasMany|FinPlan
    {
        return $this->hasMany(FinPlan::class, 'shop_id', 'id');
    }

    /**
     * 決済情報への連携
     */
    public function payments(): HasMany|FinPayment
    {
        return $this->hasMany(FinPayment::class, 'shop_id', 'id');
    }

    /**
     * サブスクリプション契約への連携
     */
    public function subscriptions(): HasMany|FinSubscription
    {
        return $this->hasMany(FinSubscription::class, 'shop_id', 'id');
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

    /**
     * ショップがプラットフォーム型であることを判定する
     */
    protected function isPlatform(): Attribute
    {
        return Attribute::make(
            fn () => $this->shop_type === ShopType::PLATFORM,
        );
    }

    /**
     * ショップがテナント型であることを判定する
     */
    protected function isTenant(): Attribute
    {
        return Attribute::make(
            fn () => $this->shop_type === ShopType::TENANT,
        );
    }

    /**
     * テナント/プラットフォーム以外のショップを取得する
     */
    protected function scopeWhereShopOther(Builder $query): Builder
    {
        return $query->whereNotin('shop_type', ShopType::cases());
    }

    /**
     * テナント型のショップのみ取得する
     */
    protected function scopeWhereShopTenant(Builder $query): Builder
    {
        return $query->where('shop_type', '=', ShopType::TENANT);
    }

    /**
     * プラットフォーム型のショップのみ取得する
     */
    protected function scopeWhereShopPlatform(Builder $query): Builder
    {
        return $query->where('shop_type', '=', ShopType::PLATFORM);
    }
}
