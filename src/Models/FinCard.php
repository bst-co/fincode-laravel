<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Casts\AsCardExpireCast;
use Fincode\Laravel\Database\Factories\FinCardFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinCard extends Model
{
    /**
     * @use HasFactory<FinCardFactory>
     */
    use HasFactory, HasHistories, HasMilliDateTime, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'type' => Fincode\Model\CardType::class,
        'brand' => Fincode\Model\CardBrand::class,
        'expire' => AsCardExpireCast::class,
        'default_flag' => 'boolean',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinCardFactory
    {
        return new FinCardFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void {}

    /**
     * 顧客情報を取得する
     */
    public function customer(): BelongsTo|FinCustomer
    {
        return $this->belongsTo(FinCustomer::class, 'customer_id', 'id');
    }
}
