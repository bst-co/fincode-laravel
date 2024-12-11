<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Fincode\Laravel\Database\Factories\FinPaymentCardFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentCard extends FinPaymentModel
{
    use HasFactory, HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

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
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['payment_id'], ['updated']);
    }
}
