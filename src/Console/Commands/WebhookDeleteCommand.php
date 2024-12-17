<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\Request\FincodeWebhookRequest;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OpenAPI\Fincode\Model\FincodeEvent;
use Throwable;

class WebhookDeleteCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:webhook:delete {event : Webhook event}';

    protected $description = 'Deleting0 Webhook information from Fincode.';

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     */
    protected function process(): Model|Collection
    {
        $token = $this->getToken();

        $event = $this->argument('event');

        $events = collect();

        foreach (FincodeEvent::cases() as $case) {
            if (Str::is($event, $case->value)) {
                $events->push($case);
            }
        }

        $models = Collection::make();

        if ($events->isNotEmpty()) {
            $request = new FincodeWebhookRequest($this->getToken());

            $webhooks = FinWebhook::whereShopId($token->shop_id)->whereIn('event', $events->pluck('value'))->pluck('id', 'event');

            $events->each(function ($event) use ($webhooks, $request, $models) {
                if ($webhooks->has($event->value)) {
                    try {
                        $model = $request->delete($webhooks->get($event->value));

                        if ($this->isSave()) {
                            $model->delete();
                        }

                        $models->push($model);
                    } catch (Throwable $e) {
                        $this->exceptions->put($event->value, $e);
                    }
                } else {
                    $this->exceptions->put($event->value, new \Exception('Webhook not exists.'));
                }
            });
        }

        return $models;
    }
}
