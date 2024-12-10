<?php

namespace LaravelFincode\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelFincode\Concerns\HasMilliDateTime;
use LaravelFincode\Concerns\HasRejectDuplicates;
use OpenAPI\Fincode;

class FinCard extends Model
{
    use HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'brand' => Fincode\Model\CardBrand::class,
        'default_flag' => 'boolean',
        'expire' => 'date',
        'created' => 'datetime',
        'updated' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function booted(): void
    {
        static::duplicates(['card_id', 'updated']);
    }
}
