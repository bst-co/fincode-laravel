<?php

namespace Fincode\Laravel\Concerns;

use Fincode\Laravel\Jobs\RejectDuplicateJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model&HasRejectDuplicates
 */
trait HasRejectDuplicates
{
    /**
     * 重複検証項目
     *
     * @var array{"group":string[], "unique":string[]}
     */
    private static array $duplicates = ['group' => [], 'unique' => []];

    /**
     * Initializes the model event callback to prevent the creation of duplicate entries.
     *
     * This method attaches a "creating" event listener that checks for duplicates
     * based on specified model attributes. If a duplicate is found, the existing
     * record is updated with a timestamp to reflect the touch operation, and the
     * creation of the new model instance is prevented.
     */
    public static function bootHasRejectDuplicates(): void
    {
        static::creating(function (Model $model): bool {
            if ($parent = static::duplicate($model, true)->first()) {
                $parent->touchQuietly();

                return false;
            }

            return true;
        });

        static::created(function (Model $model): void {
            RejectDuplicateJob::dispatch($model);
        });
    }

    /**
     * @param  array  $group  同一グループとみなす項目名リスト
     * @param  array  $unique  同一データとみなす項目名リスト
     */
    protected static function duplicates(array $group, array $unique = []): void
    {
        static::$duplicates = [
            'group' => $group,
            'unique' => array_unique([...$group, ...$unique]),
        ];
    }

    private static function getDuplicateFields(bool $unique = false)
    {
        return $unique ? static::$duplicates['unique'] : static::$duplicates['group'];
    }

    /**
     * 重複行のみ抽出する
     */
    public function getDuplicateAttributes(bool $unique = false): array
    {
        $fields = static::getDuplicateFields($unique);
        $attributes = [];

        foreach ($fields as $field) {
            $attributes[$field] = $this->{$field};
        }

        return $attributes;
    }

    public function scopeDuplicate(Builder $builder, Model $base, bool $unique = false): Builder
    {
        $fields = $base->getDuplicateAttributes($unique);

        foreach ($fields as $field => $value) {
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
