<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeTenantRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TenantRetrieveCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:tenant:retrieve {tenant_id : Tenant Shop ID}';

    protected $description = 'Retrieve Tenant shop information from Fincode.';

    protected $hidden = true;

    /**
     * {@inheritDoc}
     */
    protected function process(): Model|Collection
    {
        $request = new FincodeTenantRequest($this->getToken());

        $model = $request->retrieve($this->argument('tenant_id'));

        if ($this->isSave() && $model->save()) {
            $this->comment("Saved tenant ({$model->getTable()}.{$model->getKeyName()}={$model->getKey()}) successful.", 'v');
        }

        return $model;
    }
}
