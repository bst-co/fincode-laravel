<?php

namespace Fincode\Laravel\Test\Unit\Eloquent;

use Fincode\Laravel\Eloquent\HasHistories;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class HasHistoriesTest extends TestCase
{
    public function test_model_with_histories_can_be_instantiated(): void
    {
        Model::clearBootedModels();

        try {
            $model = new HistoryBootTestModel;

            $this->assertInstanceOf(HistoryBootTestModel::class, $model);
        } finally {
            Model::clearBootedModels();
        }
    }
}

class HistoryBootTestModel extends Model
{
    use HasHistories;
}
