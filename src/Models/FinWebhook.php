<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinWebhookFactory;
use Fincode\Laravel\Eloquent\HasFinModels;
use Fincode\OpenAPI\Model\FincodeEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinWebhook extends Model
{
    /**
     * @use HasFactory<FinWebhookFactory>
     */
    use HasFactory, HasFinModels, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'event' => FincodeEvent::class,
        'signature' => 'encrypted',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'shop_id',
        'url',
        'event',
        'signature',
        'created',
        'updated',
    ];

    /**
     * {@inheritdoc}
     */
    public static function newFactory(): FinWebhookFactory
    {
        return new FinWebhookFactory;
    }

    /**
     * ショップとの連携
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'shop_id', 'id');
    }
}
