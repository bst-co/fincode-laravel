<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentApplePay extends FinPaymentModel
{
    use HasHistories, HasMilliDateTime, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'brand' => Fincode\Model\CardBrand::class,
        'expire' => AsCardExpireCast::class,
        'method' => Fincode\Model\CardPayMethod::class,
        'pay_times' => Fincode\Model\CardPayTimes::class,
        'created' => 'datetime',
        'updated' => 'datetime',
        'auth_max_date' => 'datetime',
    ];

    /**
     * 保有する顧客情報を取得する
     */
    public function card(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCard::class, 'card_id', 'id');
    }
}
