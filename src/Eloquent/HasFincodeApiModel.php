<?php

namespace Fincode\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasFincodeApiModel
{
    /**
     * IDでオブジェクトを取得、またはFincodeAPIから対象を取得する
     *
     * @param  string  $id  オブジェクトID
     * @param  bool  $sync  FincodeAPIの情報により最新化する場合に trueを指定
     */
    abstract public static function findOrRetrieve(string $id, bool $sync = false): static;

    /**
     * FincodeAPI からデータを取得する
     */
    abstract public function syncRetrieve(): static;

    /**
     * FincodeAPI からデータを取得する(イベントを発火しない)
     */
    public function syncRetrieveQuietly(): bool
    {
        return $this->withoutEvents(fn () => $this->syncRetrieve());
    }
}
