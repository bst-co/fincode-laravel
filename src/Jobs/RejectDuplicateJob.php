<?php

namespace Fincode\Laravel\Jobs;

use Fincode\Laravel\Concerns\HasRejectDuplicates;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RejectDuplicateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  Model&HasRejectDuplicates  $model
     */
    public function __construct(
        private Model $model
    ) {
        $this->model->withoutRelations();
    }

    public function handle(): void
    {
        if (method_exists($this->model, 'getDeletedAtColumn')) {
            ($this->model::class)::duplicate($this->model)
                ->withoutTrashed()
                ->update([$this->model->getDeletedAtColumn() => now()]);
        }
    }
}
