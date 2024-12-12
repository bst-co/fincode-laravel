<?php

namespace Fincode\Laravel\Http\Request;

use Fincode\Laravel\Exceptions\FincodeRequestException;
use Fincode\Laravel\Models\FinCustomer;
use GuzzleHttp\Exception\GuzzleException;
use OpenAPI\Fincode\ApiException;
use OpenAPI\Fincode\Model\CustomerCreatingRequest;
use OpenAPI\Fincode\Model\CustomerDeletingResponse;
use OpenAPI\Fincode\Model\CustomerRetrievingResponse;
use OpenAPI\Fincode\Model\CustomerUpdatingRequest;
use OpenAPI\Fincode\Model\CustomerUpdatingResponse;
use OpenAPI\Fincode\Model\DeleteFlag;

class FincodeCustomerRequest extends FincodeAbstract
{
    /**
     * 指定顧客の最新情報を取得する
     *
     * @throws FincodeRequestException
     */
    public function get(FinCustomer|string $customer, bool $save = false): ?FinCustomer
    {
        $customer_id = $customer instanceof FinCustomer ? $customer->id : $customer;

        try {
            $response = $this->token->default()
                ->retrieveCustomer($customer_id, $this->token->private_shop_id);

            if ($response instanceof CustomerRetrievingResponse) {
                $result = (new FinCustomer)
                    ->forceFill([
                        'id' => $response->getId(),
                        'name' => $response->getName(),
                        'email' => $response->getEmail(),
                        'phone_cc' => $response->getPhoneCc(),
                        'phone_no' => $response->getPhoneNo(),
                        'addr_country' => $response->getAddrCountry(),
                        'addr_city' => $response->getAddrCity(),
                        'addr_state' => $response->getAddrState(),
                        'addr_line_1' => $response->getAddrLine1(),
                        'addr_line_2' => $response->getAddrLine2(),
                        'addr_line_3' => $response->getAddrLine3(),
                        'addr_post_code' => $response->getAddrPostCode(),
                        'card_registration' => $response->getCardRegistration(),
                        'directdebit_registration' => $response->getDirectdebitRegistration(),
                        'created' => $response->getCreated(),
                        'updated' => $response->getUpdated(),
                    ]);

                if ($save) {
                    $result->save();
                }

                return $result;
            }
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        return null;
    }

    /**
     * 顧客情報を新規作成する
     *
     * @throws FincodeRequestException
     */
    public function create(FinCustomer $customer, bool $save = true): ?FinCustomer
    {
        $body = (new CustomerCreatingRequest)
            ->setName($customer->name)
            ->setEmail($customer->email)
            ->setPhoneCc($customer->phone_cc)
            ->setPhoneNo($customer->phone_no)
            ->setAddrCountry($customer->addr_country)
            ->setAddrState($customer->addr_state)
            ->setAddrCity($customer->addr_city)
            ->setAddrLine1($customer->addr_line_1)
            ->setAddrLine2($customer->addr_line_2)
            ->setAddrLine3($customer->addr_line_3)
            ->setAddrPostCode($customer->addr_post_code);

        try {
            $response = $this->token->default()
                ->createCustomer($this->token->private_shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerCreatingRequest) {
            $result = (new FinCustomer)
                ->forceFill([
                    'id' => $response->getId(),
                    'name' => $response->getName(),
                    'email' => $response->getEmail(),
                    'phone_cc' => $response->getPhoneCc(),
                    'phone_no' => $response->getPhoneNo(),
                    'addr_country' => $response->getAddrCountry(),
                    'addr_city' => $response->getAddrCity(),
                    'addr_state' => $response->getAddrState(),
                    'addr_line_1' => $response->getAddrLine1(),
                    'addr_line_2' => $response->getAddrLine2(),
                    'addr_line_3' => $response->getAddrLine3(),
                    'addr_post_code' => $response->getAddrPostCode(),
                    'created' => now(),
                    'updated' => now(),
                ]);

            if ($save) {
                $result->save();
            }

            return $result;
        }

        return null;
    }

    public function update(FinCustomer $customer, bool $save = true): ?FinCustomer
    {
        $body = (new CustomerUpdatingRequest)
            ->setName($customer->name)
            ->setEmail($customer->email)
            ->setPhoneCc($customer->phone_cc)
            ->setPhoneNo($customer->phone_no)
            ->setAddrCountry($customer->addr_country)
            ->setAddrState($customer->addr_state)
            ->setAddrCity($customer->addr_city)
            ->setAddrLine1($customer->addr_line_1)
            ->setAddrLine2($customer->addr_line_2)
            ->setAddrLine3($customer->addr_line_3)
            ->setAddrPostCode($customer->addr_post_code);

        try {
            $response = $this->token->default()
                ->updateCustomer($customer->id, $this->token->private_shop_id, $body);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerUpdatingResponse) {
            $result = (new FinCustomer)->forceFill([
                'id' => $response->getId(),
                'name' => $response->getName(),
                'email' => $response->getEmail(),
                'phone_cc' => $response->getPhoneCc(),
                'phone_no' => $response->getPhoneNo(),
                'addr_country' => $response->getAddrCountry(),
                'addr_city' => $response->getAddrCity(),
                'addr_state' => $response->getAddrState(),
                'addr_line_1' => $response->getAddrLine1(),
                'addr_line_2' => $response->getAddrLine2(),
                'addr_line_3' => $response->getAddrLine3(),
                'addr_post_code' => $response->getAddrPostCode(),
                'card_registration' => $response->getCardRegistration(),
                'directdebit_registration' => $response->getDirectdebitRegistration(),
                'created' => $response->getCreated(),
                'updated' => $response->getUpdated(),
            ]);

            if ($save) {
                $result->save();
            }

            return $result;
        }

        return null;
    }

    /**
     * 顧客情報を削除する
     *
     * @throws FincodeRequestException
     */
    public function delete(FinCustomer $customer): bool
    {
        try {
            $response = $this->token->default()
                ->deleteCustomer($customer->id, $this->token->private_shop_id);
        } catch (GuzzleException|ApiException $e) {
            throw new FincodeRequestException($e);
        }

        if ($response instanceof CustomerDeletingResponse) {
            if ($response->getDeleteFlag() === DeleteFlag::_1) {
                FinCustomer::whereCustomerId($response->getId())->delete();

                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
