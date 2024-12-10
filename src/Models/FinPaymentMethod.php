<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * FinPayment -> FinPaymentMethodInterface をつなぐPivotテーブル
 */
class FinPaymentMethod extends Pivot
{
    public $timestamps = false;

    protected $casts = [
        'updated' => 'datetime',
    ];

    /**
     * 決済への連携
     */
    public function payment(): BelongsTo|FinPayment
    {
        return $this->belongsTo(FinPayment::class);
    }

    /**
     * 決済方法への連携
     */
    public function method(): MorphTo|FinPaymentApplePay|FinPaymentCard|FinPaymentKonbini
    {
        return $this->morphTo('payment_method', 'method_type', 'method_id', 'id');
    }
}
