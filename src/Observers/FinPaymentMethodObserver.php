<?php

namespace Fincode\Laravel\Observers;

use Fincode\Laravel\Models\FinPaymentMethod;

class FinPaymentMethodObserver
{
    public function creating(FinPaymentMethod $model): void {}

    public function created(FinPaymentMethod $model): void {}

    public function updating(FinPaymentMethod $model): void {}

    public function updated(FinPaymentMethod $model): void {}

    public function saving(FinPaymentMethod $model): void
    {
        $model->updated = $model->method?->updated ?? now();
    }

    public function saved(FinPaymentMethod $model): void {}

    public function deleting(FinPaymentMethod $model): void {}

    public function deleted(FinPaymentMethod $model): void {}

    public function restoring(FinPaymentMethod $model): void {}

    public function restored(FinPaymentMethod $model): void {}

    public function retrieved(FinPaymentMethod $model): void {}

    public function forceDeleting(FinPaymentMethod $model): void {}

    public function forceDeleted(FinPaymentMethod $model): void {}

    public function replicating(FinPaymentMethod $model): void {}
}
