<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinPlatformTokenFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read ?FinPlatform $shop トークンを保有するプラットフォーム
 */
class FinPlatformToken extends Model
{
    /**
     * @use HasFactory<FinPlatformTokenFactory>
     */
    use HasFactory, HasHistories, HasMilliDateTime;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

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
    protected static function newFactory(): FinPlatformTokenFactory
    {
        return new FinPlatformTokenFactory;
    }

    /**
     * このトークンを保有するプラットフォームを返却
     */
    public function platform(): BelongsTo|FinPlatform
    {
        return $this->belongsTo(FinPlatform::class, 'platform_id', 'id');
    }
}
