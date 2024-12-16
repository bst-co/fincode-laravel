<?php

namespace Fincode\Laravel\Listeners;

use Fincode\Laravel\Events\FincodeWebhookEvent;
use Fincode\Laravel\Jobs\FincodeWebhookJob;

class FincodeWebhookListener
{
    public function __construct() {}

    public function handle(FincodeWebhookEvent $event): void
    {
        FincodeWebhookJob::dispatch($event->webhook, $event->payload);
    }
}
