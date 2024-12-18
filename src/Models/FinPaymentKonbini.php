<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Eloquent\HasFinPaymentModels;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinPaymentKonbini extends Model
{
    use HasFinPaymentModels, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'payment_term_day',
        'payment_term',
        'payment_date',
        'barcode',
        'barcode_format',
        'barcode_width',
        'barcode_height',
        'overpayment_flag',
        'cancel_overpayment_flag',
        'konbini_code',
        'konbini_store_code',
        'device_name',
        'os_version',
        'win_width',
        'win_height',
        'xdpi',
        'ydpi',
        'result',
        'order_serial',
        'invoice_id',
        'created',
        'updated',
    ];

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
}
