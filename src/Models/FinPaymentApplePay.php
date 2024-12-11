<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentApplePay extends FinPaymentModel
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
        'brand' => Fincode\Model\CardBrand::class,
        'expire' => 'date',
        'method' => Fincode\Model\CardPayMethod::class,
        'pay_times' => Fincode\Model\CardPayTimes::class,
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
