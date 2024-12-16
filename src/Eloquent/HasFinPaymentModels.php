<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\Laravel\Models\FinPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Model
 */
trait HasFinPaymentModels
{
    use HasFinModels;

    /**
     * 親の決済情報を参照する
     */
    public function payment(): MorphOne|FinPayment
    {
        return $this->morphOne(FinPayment::class, 'pay_method');
    }
}
