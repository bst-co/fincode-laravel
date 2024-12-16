<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinWebhookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode\Model\FincodeEvent;

class FinWebhook extends Model
{
    /**
     * @use HasFactory<FinWebhookFactory>
     */
    use HasFactory, SoftDeletes;

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
        'prefix',
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
