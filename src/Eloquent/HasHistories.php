<?php

namespace Fincode\Laravel\Eloquent;

use Fincode\Laravel\Models\FinHistory;
use Fincode\Laravel\Observers\HasHistoriesObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin Model
 */
trait HasHistories
{
    public static function bootHasHistories(): void
    {
        // 変更履歴処理をフックする
        if (method_exists(static::class, 'whenBooted')) {
            static::whenBooted(fn () => static::bindHasHistoriesObserver());
        } else {
            static::bindHasHistoriesObserver();
        }
    }

    private static function bindHasHistoriesObserver(): void
    {
        static::observe(HasHistoriesObserver::class);
    }

    /**
     * 変更履歴とのリレーション
     */
    public function histories(): MorphMany|FinHistory
    {
        return $this->morphMany(FinHistory::class, 'source')->withTrashed();
    }
}
