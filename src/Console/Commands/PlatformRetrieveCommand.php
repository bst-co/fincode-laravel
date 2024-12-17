<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Http\Request\FincodePlatformRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PlatformRetrieveCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:platform:retrieve {shop_id : Shop ID}';

    protected $description = 'Retrieve Platform information from Fincode.';

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     */
    protected function process(): Model|Collection
    {
        $request = new FincodePlatformRequest($this->getToken());

        $model = $request->retrieve($this->argument('shop_id'));

        if ($this->isSave() && $model->save()) {
            $this->comment("Saved platform ({$model->getTable()}.{$model->getKeyName()}={$model->getKey()}) successful.", 'v');
        }

        return $model;
    }
}
