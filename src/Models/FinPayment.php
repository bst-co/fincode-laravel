<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Fincode\Laravel\Database\Factories\FinPaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinPayment extends Model
{
    use HasFactory, HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'pay_type' => Fincode\Model\PayType::class,
        'job_code' => Fincode\Model\CardPaymentJobCode::class,
        'status' => Fincode\Model\PaymentStatus::class,
        'process_date' => 'datetime',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $hidden = [
        'payments',
        'payment',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinPaymentFactory
    {
        return new FinPaymentFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['payment_id'], ['updated']);
    }

    /**
     * 兄弟要素を取得する
     *
     * @noinspection PhpUnused
     */
    public function siblings(): HasMany|FinPayment
    {
        return $this->hasMany(FinPayment::class, 'payment_id', 'payment_id')->latest('updated');
    }

    /**
     * 決済情報の中間テーブル取得
     */
    public function payments(): HasMany|FinPaymentMethod
    {
        return $this->hasMany(FinPaymentMethod::class, 'payment_id', 'payment_id')
            ->with(['method' => fn (MorphTo|FinPaymentModel $query) => $query->withTrashed()])
            ->latest('updated');
    }

    public function payment(): HasOne|FinPaymentMethod
    {
        return $this->hasOne(FinPaymentMethod::class, 'payment_id', 'payment_id')
            ->whereHas('method', fn (Builder|FinPaymentModel $query) => $query->withoutTrashed())
            ->with('method')
            ->latestOfMany('updated');
    }

    /**
     * 登録されているすべての決済手段を返却する
     *
     * @return Attribute<FinPaymentModel[]>
     */
    protected function methods(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->payments()
                ->with('method', fn (MorphTo|FinPaymentModel $query) => $query->withTrashed())->get()->map(fn (FinPaymentMethod $v) => $v->method),
        );
    }

    /**
     * 最新の決済手段を返却する
     *
     * @return Attribute<FinPaymentModel>
     */
    protected function method(): Attribute
    {
        return Attribute::make(
            get: fn () => $this
                ->loadMissing('payment.method')
                ->payment
                ?->method,
        );
    }
}
