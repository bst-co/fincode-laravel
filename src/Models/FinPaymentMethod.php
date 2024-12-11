<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Observers\FinPaymentMethodObserver;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * FinPayment -> FinPaymentMethodInterface をつなぐPivotテーブル
 */
class FinPaymentMethod extends Pivot
{
    use HasUlids;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function boot(): void
    {
        parent::boot();

        static::observe(FinPaymentMethodObserver::class);
    }

    /**
     * 最新の決済情報を取得 (論理削除された値は含まない)
     */
    public function payment(): HasOne|FinPayment
    {
        return $this->hasOne(FinPayment::class, 'payment_id', 'payment_id')
            ->withoutTrashed()
            ->latestOfMany('updated');
    }

    /**
     * 決済リストを取得する (論理削除された値も含む)
     */
    public function payments(): HasMany|FinPayment
    {
        return $this->hasmany(FinPayment::class, 'payment_id', 'payment_id')
            ->withTrashed();
    }

    /**
     * 決済方法への連携
     */
    public function method(): MorphTo|FinPaymentModel
    {
        return $this->morphTo(__FUNCTION__);
    }
}
