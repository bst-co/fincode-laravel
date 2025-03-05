<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinShopTokenFactory;
use Fincode\Laravel\Eloquent\HasFinModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read ?FinShop $shop トークンを保有するプラットフォーム
 */
class FinShopToken extends Model
{
    /**
     * @use HasFactory<FinShopTokenFactory>
     */
    use HasFactory, HasFinModels;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    public $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'tenant_name',
        'public_key',
        'secret_key',
        'client_field',
        'live',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'public_key' => 'encrypted',
        'secret_key' => 'encrypted',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinShopTokenFactory
    {
        return new FinShopTokenFactory;
    }

    /**
     * このトークンを保有するプラットフォームを返却
     */
    public function shop(): BelongsTo|FinShop
    {
        return $this->belongsTo(FinShop::class, 'id', 'id');
    }
}
