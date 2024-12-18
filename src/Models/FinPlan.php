<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinPlanFactory;
use Fincode\Laravel\Eloquent\HasFinModels;
use Fincode\OpenAPI\Model\IntervalCount;
use Fincode\OpenAPI\Model\IntervalPattern;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinPlan extends Model
{
    /**
     * @use HasFactory<FinPlanFactory>
     */
    use HasFactory, HasFinModels, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'plan_name',
        'description',
        'shop_id',
        'amount',
        'tax',
        'total_amount',
        'interval_pattern',
        'interval_count',
        'used_flag',
        'delete_flag',
        'created',
        'updated',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'interval_pattern' => IntervalPattern::class,
        'interval_count' => IntervalCount::class,
        'used_flag' => 'boolean',
        'delete_flag' => 'boolean',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinPlanFactory
    {
        return new FinPlanFactory;
    }

    /**
     * ショップへの連携
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'shop_id', 'id');
    }

    /**
     * サブスクリプション契約への連携
     */
    public function subscriptions(): HasMany|FinSubscription
    {
        return $this->hasMany(FinSubscription::class, 'plan_id', 'id');
    }
}
