<?php

namespace Fincode\Laravel\Eloquent;

trait HasValidations
{
    abstract public function getRules(): array;
}
