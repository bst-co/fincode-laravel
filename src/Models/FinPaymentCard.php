<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Database\Factories\FinPaymentCardFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPaymentCard extends FinPaymentModel
{
    use HasFactory, HasHistories, HasMilliDateTime, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'brand' => Fincode\Model\CardBrand::class,
        'expire' => AsCardExpireCast::class,
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
    protected static function booted(): void {}

    /**
     * 保有する顧客情報を取得する
     */
    public function customer(): BelongsTo|FinCustomer
    {
        return $this->fincode_relays->customer();
    }
}
