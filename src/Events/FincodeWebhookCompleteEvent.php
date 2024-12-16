<?php

namespace Fincode\Laravel\Events;

use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class FincodeWebhookCompleteEvent
{
    use Dispatchable;

    public function __construct(
        public FinWebhook $webhook,
        public Model $model,
    ) {}
}
