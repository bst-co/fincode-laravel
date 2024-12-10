<?php

namespace LaravelFincode\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasRejectDuplicates
{
    /**
     * 重複検証項目
     */
    private static array $duplicates = [];

    /**
     * Initializes the model event callback to prevent the creation of duplicate entries.
     *
     * This method attaches a "creating" event listener that checks for duplicates
     * based on specified model attributes. If a duplicate is found, the existing
     * record is updated with a timestamp to reflect the touch operation, and the
     * creation of the new model instance is prevented.
     */
    public function bootHasRejectDuplicates(): void
    {
        static::creating(function (Model $model): bool {
            if ($parent = self::duplicate($model)->first()) {
                $parent->touchQuietly();

                return false;
            }

            return true;
        });

        static::created(function (Model $model): void {
            if (method_exists($model, 'getDeletedAtColumn')) {
                self::duplicate($model)
                    ->withoutTrashed()
                    ->update([$model->getDeletedAtColumn() => now()]);
            }
        });
    }

    protected static function duplicates(array $fields): void
    {
        static::$duplicates = $fields;
    }

    public function scopeDuplicate(Builder $builder, self $base): Builder
    {
        $duplicates = static::$duplicates;

        if (empty($duplicates)) {
            return $builder;
        }

        foreach ($duplicates as $field) {
            $value = $base->{$field};

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format($base->getDateFormat());
            }

            $builder->where($field, $value);
        }

        if ($base->exists) {
            $builder->where($base->primaryKey, '!=', $base->{$base->primaryKey});
        }

        return $builder;
    }
}
