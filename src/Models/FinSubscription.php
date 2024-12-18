<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinSubscriptionFactory;
use Fincode\Laravel\Eloquent\HasFinModels;
use Fincode\OpenAPI\Model\PayType;
use Fincode\OpenAPI\Model\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinSubscription extends Model
{
    /**
     * @use HasFactory<FinSubscriptionFactory>
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
        'shop_id',
        'plan_id',
        'plan_name',
        'customer_id',
        'card_id',
        'payment_method_id',
        'amount',
        'tax',
        'total_amount',
        'initial_amount',
        'initial_tax',
        'initial_total_amount',
        'status',
        'start_date',
        'next_charge_date',
        'stop_date',
        'end_month_flag',
        'error_code',
        'client_field_1',
        'client_field_2',
        'client_field_3',
        'remarks',
        'created',
        'updated',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'pay_type' => PayType::class,
        'status' => SubscriptionStatus::class,
        'start_date' => 'datetime',
        'next_charge_date' => 'datetime',
        'stop_date' => 'datetime',
        'end_month_flag' => 'boolean',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * ショップへの連携
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'shop_id', 'id');
    }

    /**
     * サブスクリプションプランへの連携
     */
    public function plan(): BelongsTo|FinPlan
    {
        return $this->belongsTo(FinPlan::class, 'plan_id', 'id');
    }

    /**
     * 顧客への連携
     */
    public function customer(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCustomer::class, 'customer_id', 'id');
    }

    /**
     * 決済カード情報への連携
     */
    public function card(): BelongsTo|FinCard
    {
        return $this->belongsTo(FinCard::class, 'card_id', 'id');
    }
}
