<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinPaymentFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPayment extends Model
{
    use HasFactory, HasHistories, HasMilliDateTime,  SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'pay_type' => Fincode\Model\PayType::class,
        'job_code' => Fincode\Model\CardPaymentJobCode::class,
        'status' => Fincode\Model\PaymentStatus::class,
        'process_date' => 'datetime',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinPaymentFactory
    {
        return new FinPaymentFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void {}

    /**
     * 販売したショップ情報を取得
     */
    public function platform(): BelongsTo|FinPlatform
    {
        return $this->belongsTo(FinPlatform::class, 'shop_id', 'id');
    }

    /**
     * 決済を行った得意先情報を取得する
     */
    public function customer(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCustomer::class, 'customer_id', 'id');
    }

    /**
     * 決済情報データと紐づける
     */
    public function pay_method(): MorphTo|FinPaymentModel
    {
        return $this->morphTo(__FUNCTION__);
    }
}
