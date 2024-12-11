<?php

namespace Fincode\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class FinPaymentModel extends Model
{
    use SoftDeletes;

    protected function parents(): MorphMany|FinPaymentMethod
    {
        return $this->morphMany(FinPaymentMethod::class, 'method');
    }
}
