<?php

namespace Fincode\Laravel\Concerns;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @mixin Factory
 */
trait HasFactoryExtend
{
    protected static ?DateTimeInterface $created = null;

    /**
     * ID文字列を作成する
     *
     * @param  int  $length  ID長
     * @param  string  $prefix  前置詞
     * @param  string|null  $value  IDの値、空の場合はランダムな値が適用される
     */
    protected function getIdentify(int $length = 50, string $prefix = '', ?string $value = null): string
    {
        return Str::substr($prefix.($value ?: $this->faker->sha256()), 0, $length);
    }
}
