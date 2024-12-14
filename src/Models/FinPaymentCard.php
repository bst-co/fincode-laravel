<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Database\Factories\FinPaymentCardFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentCard extends FinPaymentModel
{
    /**
     * @use HasFactory<FinPaymentCardFactory>
     */
    use HasFactory, HasHistories, HasMilliDateTime, HasUlids, SoftDeletes;

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
        'brand' => Fincode\Model\CardBrand::class,
        'expire' => AsCardExpireCast::class,
        'method' => Fincode\Model\CardPayMethod::class,
        'pay_times' => Fincode\Model\CardPayTimes::class,
        'tds_type' => Fincode\Model\TdsType::class,
        'tds2_type' => Fincode\Model\Tds2Type::class,
        'tds2_status' => Fincode\Model\ThreeDSecure2Status::class,
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
