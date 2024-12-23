<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Database\Factories\FinPaymentCardFactory;
use Fincode\Laravel\Eloquent\HasFinPaymentModels;
use Fincode\OpenAPI;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinPaymentCard extends Model
{
    /**
     * @use HasFactory<FinPaymentCardFactory>
     */
    use HasFactory, HasFinPaymentModels, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'card_id',
        'brand',
        'card_no',
        'expire',
        'holder_name',
        'card_no_hash',
        'method',
        'pay_times',
        'bulk_payment_id',
        'subscription_id',
        'tds_type',
        'tds2_type',
        'tds2_ret_url',
        'return_url',
        'return_url_on_failure',
        'tds2_status',
        'merchant_name',
        'forward',
        'issuer',
        'transaction_id',
        'approve',
        'auth_max_date',
        'item_code',
        'created',
        'updated',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'brand' => OpenAPI\Model\CardBrand::class,
        'expire' => AsCardExpireCast::class,
        'method' => OpenAPI\Model\CardPayMethod::class,
        'pay_times' => OpenAPI\Model\CardPayTimes::class,
        'tds_type' => OpenAPI\Model\TdsType::class,
        'tds2_type' => OpenAPI\Model\Tds2Type::class,
        'tds2_status' => OpenAPI\Model\ThreeDSecure2Status::class,
        'created' => 'datetime',
        'updated' => 'datetime',
        'auth_max_date' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinPaymentCardFactory
    {
        return new FinPaymentCardFactory;
    }

    /**
     * 保有する顧客情報を取得する
     */
    public function card(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCard::class, 'card_id', 'id');
    }
}
