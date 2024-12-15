<?php

namespace Fincode\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use Fincode\Laravel\Models\FinWebhook;

class FincodeWebhookController extends Controller
{
    public function __invoke(string $prefix, string $event)
    {
        $webhook = FinWebhook::withoutTrashed()
            ->wherePrefix($prefix)
            ->whereEvent($event)
            ->first();

        if (empty($webhook)) {
            return response()->json(['message' => 'Not found'], 404);
        }
    }
}
