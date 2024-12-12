<?php

namespace Fincode\Laravel\Casts;

use DateTimeInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AsCardExpireCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
    {
        return ($value instanceof DateTimeInterface || is_string($value)) ? Carbon::parse($value) : null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (is_string($value)) {
            $value = match (strlen($value)) {
                4 => Carbon::createFromFormat('ym', $value),
                6 => Carbon::createFromFormat('Ym', $value),
                default => Carbon::parse($value),
            };
        }

        if ($value instanceof DateTimeInterface) {
            return ['expire' => Carbon::parse($value)->lastOfMonth()];
        }

        return ['expire' => null];
    }
}
