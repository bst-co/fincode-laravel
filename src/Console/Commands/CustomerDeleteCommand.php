<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeCustomerRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'fincode:customer:delete', description: 'Delete customer information to Fincode.')]
class CustomerDeleteCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:customer:delete';

    protected $description = 'Delete customer information to Fincode.';

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     * @throws FincodeUnknownResponseException
     */
    protected function process(): Model|Collection
    {
        $request = new FincodeCustomerRequest($this->getToken());

        return tap(
            $request->delete($this->argument('customer_id')),
            function (?Model $model) {
                $this->comment('Deleting customer request successful.', 'v');

                if ($model && $model->delete()) {
                    $this->comment("Deleted customer ({$model->getTable()}.{$model->getKeyName()}={$model->getKey()}) successful.", 'v');
                }
            }
        );
    }
}
