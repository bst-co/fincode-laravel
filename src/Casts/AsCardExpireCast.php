<?php

namespace Fincode\Laravel\Casts;

use DateTimeInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AsCardExpireCast implements CastsAttributes
{
    /**
     * {@inheritdoc}
     *
     * @return Carbon|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return ($value instanceof DateTimeInterface || is_string($value)) ? Carbon::parse($value) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @param  Model  $model
     * @param  mixed  $value
     * @return array|null[]
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_string($value)) {
            // 4桁の 'ym'フォーマットと思しき場合、前方に西暦の前2桁を追加して'Ym'フォーマットに修正する
            if (strlen($value) === 4) {
                $value = substr(now()->format('Y'), 0, 2).$value;
            }

            $value = match (strlen($value)) {
                6 => Carbon::createFromFormat('Ym', $value),
                default => Carbon::parse($value),
            };
        }

        if ($value instanceof DateTimeInterface) {
            $value = Carbon::parse($value)->lastOfMonth();

            if (isset($attributes[$key]) && $value->equalTo($attributes[$key])) {
                return [$key => $attributes[$key]];
            }

            return [$key => $value];
        }

        return [$key => null];
    }
}
