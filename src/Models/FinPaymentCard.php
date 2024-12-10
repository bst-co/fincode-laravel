<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFincode\Concerns\HasMilliDateTime;
use LaravelFincode\Concerns\HasRejectDuplicates;
use OpenAPI\Fincode;

class FinPaymentCard extends Model implements FinPaymentMethodInterface
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
    protected static function booted(): void
    {
        static::duplicates(['payment_id', 'updated']);
    }
}
