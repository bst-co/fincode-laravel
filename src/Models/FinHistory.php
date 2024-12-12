<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Fincode\Laravel\Enums\FinHistoryType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinHistory extends Model
{
    use HasHistories, HasMilliDateTime, HasUlids, SoftDeletes;

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'type' => FinHistoryType::class,
        'difference' => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s.u';
    }

    /**
     * 参照元へのリレーション
     */
    public function source(): MorphTo
    {
        return $this->morphTo(__FUNCTION__);
    }
}
