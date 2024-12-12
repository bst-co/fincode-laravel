<?php

namespace Fincode\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class FinPaymentModel extends Model
{
    use SoftDeletes;

    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo|FinPayment
    {
        return $this->belongsTo(FinPayment::class, 'id', 'id');
    }
}
