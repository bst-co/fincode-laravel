<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFincode\Concerns\HasMilliDateTime;
use LaravelFincode\Concerns\HasRejectDuplicates;
use OpenAPI\Fincode;

class FinPaymentKonbini extends Model implements FinPaymentMethodInterface
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
        'konbini_code' => Fincode\Model\KonbiniCode::class,
        'result' => Fincode\Model\KonbiniPaymentProcessResult::class,
        'overpayment_flag' => Fincode\Model\OverpaymentFlag::class,
        'cancel_overpayment_flag' => Fincode\Model\CancelOverpaymentFlag::class,
        'created' => 'datetime',
        'updated' => 'datetime',
        'auth_max_date' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['payment_id', 'updated']);
    }
}
