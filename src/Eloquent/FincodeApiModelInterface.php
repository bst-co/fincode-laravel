<?php

namespace Fincode\Laravel\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
interface FincodeApiModelInterface
{
    /**
     * IDでオブジェクトを取得、またはFincodeAPIから対象を取得する
     *
     * @param  string  $id  オブジェクトID
     * @param  bool  $sync  FincodeAPIの情報により最新化する場合に trueを指定
     */
    public static function findOrRetrieve(string $id, bool $sync = false): static;

    /**
     * FincodeAPI からデータを取得する
     */
    public function syncRetrieve(): bool;

    /**
     * FincodeAPI からデータを取得、失敗時に例外を返す
     */
    public function syncRetrieveOrFail(): bool;

    /**
     * FincodeAPI からデータを取得する(イベントを発火しない)
     */
    public function syncRetrieveQuietly(): bool;
}
