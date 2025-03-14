<?php

namespace Fincode\Laravel\Http\Controllers;

use Fincode\Laravel\Events\FincodeWebhookEvent;
use Fincode\Laravel\Models\FinWebhook;
use Fincode\OpenAPI\Model\FincodeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FincodeWebhookController
{
    public function __invoke(Request $request, string $shop, FincodeEvent $event)
    {
        // Check Method
        if (! $request->isMethod('POST')) {
            return response()->json(['message' => 'Invalid method'], 405);
        }

        // Check Request Content-Type
        if ($request->header('content-type') !== 'application/json') {
            return response()->json(['message' => 'Invalid content type'], 400);
        }

        $signature = $request->header('fincode-signature', '');

        $webhook = FinWebhook::whereShopId($shop)
            ->whereEvent($event)
            ->whereHas('shop')
            ->first();

        // Check webhook Endpoint
        if (! $webhook) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Check Webhook Signature
        if ($webhook->signature !== $signature) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        FincodeWebhookEvent::dispatch(Str::orderedUuid()->toString(), $webhook, $request->all());

        return response()->json(['receive' => '1']);
    }
}
