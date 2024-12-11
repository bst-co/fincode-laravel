<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentKonbini extends FinPaymentModel
{
    use HasMilliDateTime, HasRejectDuplicates, HasUlids;

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
        static::duplicates(['payment_id'], ['updated']);
    }
}
