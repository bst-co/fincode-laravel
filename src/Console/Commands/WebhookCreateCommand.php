<?php

namespace Fincode\Laravel\Console\Commands;

use Exception;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\Request\FincodeWebhookRequest;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OpenAPI\Fincode\Model\FincodeEvent;
use Throwable;

class WebhookCreateCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:webhook:create {event : Webhook event}';

    protected $description = 'Command description';

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
                if (FinWebhook::whereShopId($token->shop_id)->whereEvent($case->value)->doesntExist()) {
                    $events->push($case);
                } else {
                    $this->exceptions->put($case->value, new Exception('Webhook existed.'));
                }
            }
        }

        $models = Collection::make();

        if ($events->isNotEmpty()) {
            $request = new FincodeWebhookRequest($this->getToken());

            $events->each(function ($event) use ($request, $models) {
                try {
                    $model = $request->create($event);

                    if ($this->isSave()) {
                        $model->trashed() ? $model->restore() : $model->save();
                    }

                    $models->push($model);
                } catch (Throwable $e) {
                    $this->exceptions->put($event->value, $e);
                }
            });
        }

        return $models;
    }
}
