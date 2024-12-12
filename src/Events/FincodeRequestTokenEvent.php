<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinPlatform;
use Fincode\Laravel\Models\FinPlatformToken;
use Illuminate\Foundation\Events\Dispatchable;

class FincodeRequestTokenEvent
{
    use Dispatchable;

    public function __construct(
        public string|FinPlatform|FinPlatformToken|null $token
    ) {}
}
