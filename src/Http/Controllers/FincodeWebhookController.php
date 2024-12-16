<?php

namespace Fincode\Laravel\Http\Controllers;

use App\Http\Controllers\Controller;
use Fincode\Laravel\Models\FinWebhook;

class FincodeWebhookController extends Controller
{
    public function __invoke(string $hash, string $event)
    {
        $webhook = FinWebhook::withoutTrashed()
            ->whereHash($hash)
            ->whereEvent($event)
            ->first();

        if (empty($webhook)) {
            return response()->json(['message' => 'Not found'], 404);
        }
    }
}
