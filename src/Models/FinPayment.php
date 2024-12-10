<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFincode\Concerns\HasMilliDateTime;
use LaravelFincode\Concerns\HasRejectDuplicates;
use OpenAPI\Fincode;

class FinPayment extends Model
{
    use HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'pay_type' => Fincode\Model\PayType::class,
        'status' => Fincode\Model\PaymentStatus::class,
        'process_date' => 'datetime',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $appends = [
        'method',
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'payments',
        'payment',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['payment_id', 'updated']);
    }

    /**
     * 兄弟要素を取得する
     *
     * @noinspection PhpUnused
     */
    public function siblings(): HasMany|FinPayment
    {
        return $this->hasMany(FinPayment::class, 'payment_id', 'payment_id')->latest('updated');
    }

    public function payments(): HasMany|FinPaymentMethod
    {
        return $this->hasMany(FinPaymentMethod::class, 'payment_id', 'payment_id')
            ->latest('updated');
    }

    public function payment(): HasOne|FinPaymentMethod
    {
        return $this->hasOne(FinPaymentMethod::class, 'payment_id', 'payment_id')
            ->with('paymentable')
            ->latestOfMany('updated');
    }

    /**
     * 登録されているすべての決済手段を返却する
     *
     * @noinspection PhpUnused
     */
    protected function getMethodsAttribute()
    {
        return $this
            ->payments()
            ->afterQuery(fn (Collection $v) => $v->map(fn (FinPaymentMethod $v) => $v->paymentable));
    }

    /**
     * 最新の決済手段を返却する
     *
     * @noinspection PhpUnused
     */
    protected function getMethodAttribute(): FinPaymentApplePay|FinPaymentCard|FinPaymentKonbini
    {
        return $this
            ->loadMissing('payment.paymentable')
            ->payment
            ->paymentable;
    }
}
