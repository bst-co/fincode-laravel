<?php

namespace Fincode\Laravel\Models;

use Fincode\Laravel\Database\Factories\FinCustomerFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinCustomer extends Model
{
    /**
     * @use HasFactory<FinCustomerFactory>
     */
    use HasFactory, HasHistories, HasMilliDateTime, SoftDeletes;

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'customer_id',
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
     * カード情報
     */
    public function cards(): HasMany|FinCard
    {
        return $this->hasMany(FinCard::class, 'customer_id', 'id');
    }
}
