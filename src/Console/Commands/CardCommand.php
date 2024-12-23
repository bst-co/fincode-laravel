<?php

namespace Fincode\Laravel\Console\Commands;

use Fincode\Laravel\Clients\FincodeCardRequest;
use Fincode\Laravel\Concerns\FincodeApiCommand;
use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Models\FinCard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * 指定の顧客に対するカード情報を取得、データベースに保存する
 */
class CardCommand extends FincodeApiCommand
{
    protected $signature = 'fincode:card {customer_id : Customer ID}';

    protected $description = 'Get Customer Card list from Fincode.';

    protected $hidden = true;

    /**
     * {@inheritDoc}
     *
     * @throws FincodeRequestException
     */
    protected function process(): Model|Collection
    {
        $customer_id = $this->argument('customer_id');

        $request = new FincodeCardRequest($customer_id, $this->getToken());

        $models = $request->list();

        if ($this->isSave()) {
            // 取得データに存在しないカードIDを探索して削除する
            $values = FinCard::whereCustomerId($customer_id)
                ->pluck('id')
                ->diff($models->pluck('id'));

            if ($values->isNotEmpty()) {
                // 存在しないカード情報を削除
                FinCard::whereIn('id', $values)->delete();
            }

            $models->each(fn (FinCard $model) => $model->save());
        }

        return $models;
    }
}
