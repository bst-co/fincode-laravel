<?php

namespace Fincode\Laravel\Observers;

use Fincode\Laravel\Enums\FinHistoryType;
use Fincode\Laravel\Models\FinHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasHistoriesObserver
{
    public function created(Model $model): void
    {
        $this->attach(FinHistoryType::INSERT, $model);
    }

    public function updated(Model $model): void
    {
        $this->attach(FinHistoryType::UPDATE, $model);
    }

    public function deleted(Model $model): void
    {
        $this->attach(FinHistoryType::DELETE, $model);
    }

    public function restored(Model $model): void
    {
        $this->attach(FinHistoryType::RESTORE, $model);
    }

    public function forceDeleted(Model $model): void
    {
        $this->attach(FinHistoryType::FORCE_DELETE, $model);
    }

    protected function attach(FinHistoryType $type, Model $model): void
    {
        if ($model instanceof FinHistory) {
            return;
        }

        $changes = $model->getChanges();

        $deleted_at = in_array(SoftDeletes::class, class_uses_recursive($model)) ? $model->getDeletedAtColumn() : null;
        $updated_at = $model->getUpdatedAtColumn();

        // 更新日付は記録対象から除外する
        if ($updated_at) {
            unset($changes[$updated_at]);
        }

        // 削除日付は記録対象から除外する
        if ($deleted_at) {
            unset($changes[$deleted_at]);
        }

        if ($type === FinHistoryType::UPDATE && empty($changes)) {
            return;
        }

        $deleted = $type === FinHistoryType::FORCE_DELETE || ($deleted_at && $type === FinHistoryType::DELETE);

        $history = (new FinHistory)
            ->source()->associate($model)
            ->forceFill([
                'difference' => $changes,
                'type' => $type,
            ]);

        if ($history->saveQuietly() && $deleted) {
            FinHistory::whereSourceId($history->source_id)
                ->whereSourceType($history->source_type)
                ->delete();
        }
    }
}
