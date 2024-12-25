<?php

namespace Fincode\Laravel\Observers;

use Fincode\Laravel\Enums\FinHistoryType;
use Fincode\Laravel\Models\FinHistory;
use Illuminate\Database\Eloquent\Model;

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

        $deleted_at = method_exists($model, 'getDeletedAtColumn') ? $model->getDeletedAtColumn() : null;
        $updated_at = $model->getUpdatedAtColumn();

        // 非論理削除モデル時に FinHistoryType::DELETE を受け取った場合 FinHistoryType::FORCE_DELETE に置き換える
        if ($type === FinHistoryType::DELETE && $deleted_at !== null) {
            $type = FinHistoryType::FORCE_DELETE;
        }

        if ($type === FinHistoryType::FORCE_DELETE) {
            $changes = $model->getAttributes();
        } else {
            // 更新日付は記録対象から除外する
            if ($updated_at && isset($changes[$updated_at])) {
                unset($changes[$updated_at]);
            }

            // 削除日付は記録対象から除外する
            if ($deleted_at && isset($changes[$deleted_at])) {
                unset($changes[$deleted_at]);
            }
        }

        // 更新時にデータがない場合は記録しない
        if ($type === FinHistoryType::UPDATE && empty($changes)) {
            return;
        }

        // 物理削除モードかつ、ヒストリを残さないモードの場合は対象をすべて削除する
        if ($type === FinHistoryType::FORCE_DELETE && config('fincode.history.relay_delete', false)) {
            FinHistory::whereSourceId($model->getKey())
                ->whereSourceType($model->getMorphClass())
                ->forceDelete();

            return;
        }

        // 履歴モデルを作成
        $history = (new FinHistory)
            ->source()->associate($model)
            ->forceFill([
                'difference' => $changes,
                'type' => $type,
            ]);

        // 履歴データ保存、強制削除の場合は対象オブジェクトすべてを論理削除状態に移行する
        if ($history->save() && $type === FinHistoryType::FORCE_DELETE) {
            FinHistory::whereSourceId($history->source_id)
                ->whereSourceType($history->source_type)
                ->delete();
        }
    }
}
