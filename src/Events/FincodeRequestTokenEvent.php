<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinShop;
use Fincode\Laravel\Models\FinShopToken;
use Illuminate\Foundation\Events\Dispatchable;

class FincodeRequestTokenEvent
{
    use Dispatchable;

    public function __construct(
        public string|FinShop|FinShopToken|null $token
    ) {}
}
