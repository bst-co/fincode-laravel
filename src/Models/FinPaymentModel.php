<?php

namespace Fincode\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class FinPaymentModel extends Model
{
    use SoftDeletes;

    /**
     * 親の決済情報を参照する
     */
    public function payment(): MorphOne|FinPayment
    {
        return $this->morphOne(FinPayment::class, 'pay_method');
    }
}
