<?php

namespace Fincode\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasFinModels
{
    use HasHistories, HasMilliDateTime;

    public static function bootHasFinModels(): void
    {
        // 新規作成時のアクション
        static::creating(function (Model $model) {
            if (in_array('created', $model->getFillable()) && $model->getAttribute('created') === null) {
                $model->setAttribute('created', now());
            }
        });
    }
}
