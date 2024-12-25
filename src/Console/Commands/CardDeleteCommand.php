<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeCardRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CardDeleteCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:card:delete {customer_id : Customer ID} {card_id : Card ID} {--force : Force delete}';

    protected $description = 'Deleting Customer Card information from Fincode.';

    protected $hidden = true;

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     * @throws FincodeUnknownResponseException
     */
    protected function process(): Model|Collection
    {
        $customer_id = $this->argument('customer_id');

        $request = new FincodeCardRequest($customer_id, $this->getToken());

        $model = $request->delete($this->argument('card_id'));

        $this->comment('Deleting customer card request successful.', 'v');

        if ($this->isSave() && ($this->option('force') ? $model->forceDelete() : $model->delete())) {
            $this->comment("Deleted customer card ({$model->getTable()}.{$model->getKeyName()}={$model->getKey()}) successful.", 'v');
        }

        return $model;
    }
}
