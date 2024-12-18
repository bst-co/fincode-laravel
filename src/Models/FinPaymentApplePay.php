<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Eloquent\HasFinPaymentModels;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinPaymentApplePay extends Model
{
    use HasFinPaymentModels, HasUlids, SoftDeletes;

    protected $fillable = [
        'card_id',
        'brand',
        'card_no',
        'expire',
        'holder_name',
        'card_no_hash',
        'method',
        'pay_times',
        'forward',
        'issuer',
        'transaction_id',
        'approve',
        'auth_max_date',
        'item_code',
        'created',
        'updated',
    ];

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
