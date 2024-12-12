<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentKonbini extends FinPaymentModel
{
    use HasHistories, HasMilliDateTime, HasUlids, SoftDeletes;

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
    protected static function booted(): void {}
}
