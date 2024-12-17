<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinCustomerFactory;
use Fincode\Laravel\Eloquent\HasFincodeApiModel;
use Fincode\Laravel\Eloquent\HasFinModels;
use Fincode\Laravel\Http\Request\FincodeCustomerRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinCustomer extends Model
{
    /**
     * @use HasFactory<FinCustomerFactory>
     */
    use HasFactory, HasFincodeApiModel, HasFinModels, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'name',
        'email',
        'phone_cc',
        'phone_no',
        'addr_country',
        'addr_state',
        'addr_city',
        'addr_line_1',
        'addr_line_2',
        'addr_line_3',
        'addr_post_code',
        'card_registration',
        'directdebit_registration',
        'created',
        'updated',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'created' => 'datetime',
        'updated' => 'datetime',
        'card_registration' => 'boolean',
        'directdebit_registration' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): FinCustomerFactory
    {
        return new FinCustomerFactory;
    }

    /**
     * {@inheritDoc}
     */
    public static function findOrRetrieve(string $id, bool $sync = false): static
    {
        $model = static::with('shop')->find($id);

        if ($model) {
            return $sync ? $model->syncRetrieve() : $model;
        }

        return static::forceFill(['id' => $id])->syncRetrieve();
    }

    /**
     * {@inheritDoc}
     */
    public function syncRetrieve(): static
    {
        return tap((new FincodeCustomerRequest)->retrieve($this->id), fn (FinCustomer $model) => $model->save());
    }

    /**
     * カード情報
     */
    public function cards(): HasMany|FinCard
    {
        return $this->hasMany(FinCard::class, 'customer_id', 'id');
    }

    /**
     * サブスクリプション契約への連携
     */
    public function subscriptions(): HasMany|FinSubscription
    {
        return $this->hasMany(FinSubscription::class, 'plan_id', 'id');
    }
}
