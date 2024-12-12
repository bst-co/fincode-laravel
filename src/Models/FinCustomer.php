<?php

namespace Fincode\Laravel\Models;

use Carbon\Carbon;
use Fincode\Laravel\Database\Factories\FinCustomerFactory;
use Fincode\Laravel\Eloquent\HasHistories;
use Fincode\Laravel\Eloquent\HasMilliDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenAPI\Fincode;

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
     * {@inheritdoc}
     */
    protected static function booted(): void {}

    /**
     * カード情報
     */
    public function cards(): HasMany|FinCard
    {
        return $this->hasMany(FinCard::class, 'customer_id', 'id');
    }

    public function fillFinCode(
        Fincode\Model\CustomerRetrievingResponse|Fincode\Model\CustomerCreatingResponse|Fincode\Model\CustomerUpdatingResponse $fin
    ): static {
        $this->forceFill([
            'customer_id' => $fin->getId(),
            'name' => $fin->getName(),
            'email' => $fin->getEmail(),
            'phone_cc' => $fin->getPhoneCc(),
            'phone_no' => $fin->getPhoneNo(),
            'addr_country' => $fin->getAddrCountry(),
            'addr_state' => $fin->getAddrState(),
            'addr_city' => $fin->getAddrCity(),
            'addr_line_1' => $fin->getAddrLine1(),
            'addr_line_2' => $fin->getAddrLine2(),
            'addr_line_3' => $fin->getAddrLine3(),
            'addr_post_code' => $fin->getAddrPostCode(),
            'card_registration' => (bool) $fin->getCardRegistration(),
            'directdebit_registration' => (bool) $fin->getDirectdebitRegistration(),
            'created' => ($v = $fin->getCreated()) ? Carbon::parse($v) : null,
            'updated' => ($v = $fin->getUpdated()) ? Carbon::parse($v) : null,
        ]);

        return $this;
    }

    public static function fromFinCode(
        Fincode\Model\CustomerRetrievingResponse|Fincode\Model\CustomerCreatingResponse|Fincode\Model\CustomerUpdatingResponse $fin
    ): FinCustomer {
        return (new FinCustomer)
            ->fillFinCode($fin);
    }
}
