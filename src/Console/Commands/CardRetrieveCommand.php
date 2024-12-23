<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeCardRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CardRetrieveCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:card:retrieve {customer_id : Customer ID} {card_id : Card ID}';

    protected $description = 'Retrieve Customer Card information from Fincode.';

    protected $hidden = true;

    /**
     * {@inheritDoc}
     */
    protected function process(): Model|Collection
    {
        $request = new FincodeCardRequest($this->argument('customer_id'), $this->getToken());

        $model = $request->retrieve($this->argument('card_id'));

        $this->comment('Retrieving customer card request successful.', 'v');

        if ($this->isSave() && $model->save()) {
            $this->comment("Saved customer card ({$model->getTable()}.{$model->getKeyName()}={$model->getKey()}) successful.", 'v');
        }

        return $model;
    }
}
