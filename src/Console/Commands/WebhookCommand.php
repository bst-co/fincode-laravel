<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeWebhookRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Models\FinWebhook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class WebhookCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:webhook';

    protected $description = 'List Webhook information from Fincode.';

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     * @throws FincodeUnknownResponseException
     */
    protected function process(): Model|Collection
    {
        $token = $this->getToken();
        $request = new FincodeWebhookRequest($token);

        $models = $request->list();

        if ($this->isSave()) {
            $values = FinWebhook::whereShopId($token->shop_id)
                ->withTrashed()
                ->pluck('id')
                ->diff($models->pluck('id'));

            if ($values->isNotEmpty()) {
                FinWebhook::whereIn('id', $values)->delete();
            }

            $models->each(fn (FinWebhook $model) => $model->trashed() ? $model->restore() : $model->save());
        }

        return $models;
    }
}
