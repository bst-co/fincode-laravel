<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinPaymentFactory;
use Fincode\Laravel\Eloquent\HasFinModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @template MethodModel of FinPaymentCard|FinPaymentKonbini|FinPaymentApplePay
 */
class FinPayment extends Model
{
    use HasFactory, HasFinModels, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'shop_id',
        'pay_type',
        'job_code',
        'status',
        'access_id',
        'amount',
        'tax',
        'total_amount',
        'client_field_1',
        'client_field_2',
        'client_field_3',
        'process_date',
        'customer_id',
        'customer_group_id',
        'error_code',
        'created',
        'updated',
    ];

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
    protected static function newFactory(): FinPaymentFactory
    {
        return new FinPaymentFactory;
    }

    protected static function booted(): void
    {
        static::saving(function ($model): void {
            // payMethod の内容未保存の場合、保存して再割り当てを行う
            if (($method = $model->pay_method) && ! $method->exists) {
                $model->pay_method()->associate(tap($method)->save());
            }
        });
    }

    /**
     * 販売したショップ情報を取得
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'shop_id', 'id');
    }

    /**
     * 決済を行った得意先情報を取得する
     */
    public function customer(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCustomer::class, 'customer_id', 'id');
    }

    /**
     * 決済情報データと紐づける
     */
    public function pay_method(): MorphTo|FinPaymentCard|FinPaymentKonbini|FinPaymentApplePay
    {
        return $this->morphTo(__FUNCTION__);
    }

    /**
     * 保持する決済方法モデルが、指定の型と一致するか検証する
     *
     * @param  class-string<MethodModel>  $model
     * @return MethodModel|null
     */
    public function getPayMethodBy(string $model): ?Model
    {
        if ($this->pay_method && $this->pay_method instanceof $model) {
            return $this->pay_method;
        }

        return null;
    }
}
