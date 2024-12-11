<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Concerns\HasMilliDateTime;
use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Fincode\Laravel\Database\Factories\FinCardFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

class FinCard extends Model
{
    use HasFactory, HasMilliDateTime, HasRejectDuplicates, HasUlids, SoftDeletes;

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
        static::duplicates(['card_id'], ['updated']);
    }
}
