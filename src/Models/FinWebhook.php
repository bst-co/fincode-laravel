<?php

namespace Fincode\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode\Model\FincodeEvent;

class FinWebhook extends Model
{
    use SoftDeletes;

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
     * ショップとの連携
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'shop_id', 'id');
    }
}
