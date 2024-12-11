<?php

namespace Fincode\Laravel\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * モデルが使用する日付型にマイクロ秒を含める
 *
 * @mixin Model
 */
trait HasMilliDateTime
{
    /**
     * Get the format for database stored dates.
     */
    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s.v';
    }
}
