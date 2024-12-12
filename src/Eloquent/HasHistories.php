<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\Laravel\Observers\HasHistoriesObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasHistories
{
    public static function bootHasHistories(): void
    {
        static::observe(HasHistoriesObserver::class);
    }
}
