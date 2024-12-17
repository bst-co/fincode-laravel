<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Exceptions\FincodeUnknownResponseException;
use Fincode\Laravel\Http\Request\FincodeCustomerRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'fincode:customer:retrieve', description: 'Get customer information from Fincode.')]
class CustomerRetrieveCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:customer:retrieve {customer_id : Customer ID}';

    protected $description = 'Get Customer information from Fincode.';

    /**
     * {@inheritdoc}
     *
     * @throws FincodeRequestException
     * @throws FincodeUnknownResponseException
     * @throws Throwable
     */
    protected function process(): Model|Collection
    {
        $request = new FincodeCustomerRequest($this->getToken());

        return tap(
            $request->retrieve($this->argument('customer_id')),
            function (Model $model) {
                $this->comment('Retrieved customer successful.', 'v');

                if ($this->isSave()) {
                    $model->saveOrFail();
                    $this->comment('Saved customer successful.', 'v');
                }
            }
        );
    }
}
