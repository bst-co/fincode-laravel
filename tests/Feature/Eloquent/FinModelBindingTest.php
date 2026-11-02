<?php

namespace Fincode\Laravel\Test\Feature\Eloquent;

use Fincode\Laravel\Eloquent\FinModelBinding;
use Fincode\Laravel\FincodeLaravelServiceProvider;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\OpenAPI\Model\ApplePayPaymentJobCode;
use Fincode\OpenAPI\Model\CapturePayment200Response;
use Fincode\OpenAPI\Model\CardBrand;
use Fincode\OpenAPI\Model\CardPaymentJobCode;
use Fincode\OpenAPI\Model\CardPayMethod;
use Fincode\OpenAPI\Model\CreatePayment200Response;
use Fincode\OpenAPI\Model\CustomerCardCreatingResponse;
use Fincode\OpenAPI\Model\CustomerCardRetrievingResponse;
use Fincode\OpenAPI\Model\CustomerCardUpdatingResponse;
use Fincode\OpenAPI\Model\CustomerCreatingResponse;
use Fincode\OpenAPI\Model\CustomerRetrievingResponse;
use Fincode\OpenAPI\Model\CustomerUpdatingResponse;
use Fincode\OpenAPI\Model\PaymentApplePayCapturingResponse;
use Fincode\OpenAPI\Model\PaymentApplePayCreatingResponse;
use Fincode\OpenAPI\Model\PaymentApplePayRetrievingResponse;
use Fincode\OpenAPI\Model\PaymentCardCapturingResponse;
use Fincode\OpenAPI\Model\PaymentCardCreatingResponse;
use Fincode\OpenAPI\Model\PaymentCardRetrievingResponse;
use Fincode\OpenAPI\Model\PaymentStatus;
use Fincode\OpenAPI\Model\PayType;
use Fincode\OpenAPI\Model\RetrievePayment200Response;
use Fincode\OpenAPI\ObjectSerializer;
use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;

class FinModelBindingTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FincodeLaravelServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__.'/../../../database/migrations');
    }

    public function test_card_registration_response_without_method_is_saved(): void
    {
        $response = new PaymentCardCreatingResponse($this->paymentValues());
        $this->assertFalse(property_exists($response->jsonSerialize(), 'method'));

        $payment = (new FinModelBinding)->payment($response)->fresh();

        $this->assertSame(PaymentStatus::UNPROCESSED, $payment->status);
        $this->assertInstanceOf(FinPaymentCard::class, $payment->pay_method);
        $this->assertNull($payment->pay_method->method);
        $this->assertSame(100, $payment->total_amount);
    }

    public function test_card_response_with_explicit_null_method_is_saved(): void
    {
        $values = (array) (new PaymentCardCreatingResponse($this->paymentValues()))->jsonSerialize();
        $payment = (new FinModelBinding)->payment([...$values, 'method' => null])->fresh();

        $this->assertNull($payment->pay_method->method);
    }

    public function test_card_retrieve_and_capture_update_the_existing_payment(): void
    {
        $binding = new FinModelBinding;
        $payment = $binding->payment(new PaymentCardCreatingResponse($this->paymentValues()));
        $id = $payment->pay_method_id;

        $payment = $binding->payment(new PaymentCardRetrievingResponse([
            ...$this->paymentValues(),
            'status' => PaymentStatus::AUTHORIZED,
            'method' => CardPayMethod::_1,
            'brand' => CardBrand::VISA,
            'holderName' => 'TEST USER',
        ]))->fresh();

        $this->assertSame($id, $payment->pay_method_id);
        $this->assertSame(CardPayMethod::_1, $payment->pay_method->method);

        $payment = $binding->payment(new PaymentCardCapturingResponse([
            ...$this->paymentValues(),
            'status' => PaymentStatus::CAPTURED,
        ]))->fresh();

        $this->assertSame(PaymentStatus::CAPTURED, $payment->status);
        $this->assertSame($id, $payment->pay_method_id);
        $this->assertSame(CardPayMethod::_1, $payment->pay_method->method);
        $this->assertSame(CardBrand::VISA, $payment->pay_method->brand);
        $this->assertSame('TEST USER', $payment->pay_method->holder_name);
        $this->assertSame(1, FinPaymentCard::count());
    }

    public function test_valid_card_method_is_accepted_from_array_and_json(): void
    {
        $values = (array) (new PaymentCardCreatingResponse([
            ...$this->paymentValues(),
            'method' => CardPayMethod::_1,
        ]))->jsonSerialize();

        foreach ([$values, json_encode($values)] as $input) {
            $payment = (new FinModelBinding)->payment($input)->fresh();
            $this->assertSame(CardPayMethod::_1, $payment->pay_method->method);
        }
    }

    public function test_invalid_card_method_is_rejected(): void
    {
        $values = (array) (new PaymentCardCreatingResponse($this->paymentValues()))->jsonSerialize();

        $this->expectException(ValidationException::class);
        (new FinModelBinding)->payment([...$values, 'method' => '99']);
    }

    public function test_apple_pay_responses_without_card_details_are_saved(): void
    {
        foreach ([
            PaymentApplePayCreatingResponse::class => PaymentStatus::UNPROCESSED,
            PaymentApplePayRetrievingResponse::class => PaymentStatus::AUTHORIZED,
            PaymentApplePayCapturingResponse::class => PaymentStatus::CAPTURED,
        ] as $class => $status) {
            $response = new $class([
                ...$this->paymentValues(),
                'payType' => PayType::APPLEPAY,
                'jobCode' => ApplePayPaymentJobCode::AUTH,
                'status' => $status,
                'cardNo' => null,
                'expire' => null,
                'holderName' => null,
                'brand' => null,
                'cardNoHash' => null,
            ]);
            $payment = (new FinModelBinding)->payment($response)->fresh();

            $this->assertSame($status, $payment->status);
            $this->assertInstanceOf(FinPaymentApplePay::class, $payment->pay_method);
            $this->assertNull($payment->pay_method->card_id);
            $this->assertNull($payment->pay_method->brand);
            $this->assertNull($payment->pay_method->card_no);
        }
        $this->assertSame(1, FinPaymentApplePay::count());
    }

    public function test_apple_pay_brand_is_bound_from_the_response(): void
    {
        $response = new PaymentApplePayRetrievingResponse([
            ...$this->paymentValues(),
            'payType' => PayType::APPLEPAY,
            'jobCode' => ApplePayPaymentJobCode::AUTH,
            'brand' => CardBrand::VISA,
            'method' => CardPayMethod::_1,
        ]);
        $payment = (new FinModelBinding)->payment($response)->fresh();

        $this->assertSame(CardBrand::VISA, $payment->pay_method->brand);
        $this->assertSame(CardPayMethod::_1, $payment->pay_method->method);
    }

    public function test_invalid_apple_pay_brand_is_rejected(): void
    {
        $response = new PaymentApplePayCreatingResponse([
            ...$this->paymentValues(),
            'payType' => PayType::APPLEPAY,
            'jobCode' => ApplePayPaymentJobCode::AUTH,
        ]);
        $this->expectException(ValidationException::class);
        (new FinModelBinding)->payment([...(array) $response->jsonSerialize(), 'brand' => 'INVALID']);
    }

    public function test_customer_optional_details_accept_missing_and_null_values(): void
    {
        foreach ([CustomerCreatingResponse::class, CustomerRetrievingResponse::class, CustomerUpdatingResponse::class] as $class) {
            foreach ([[], ['name' => null, 'email' => null]] as $optional) {
                $model = (new FinModelBinding)->customer(new $class(['id' => 'customer123', ...$optional]));
                $this->assertSame('customer123', $model->id);
                $this->assertNull($model->name);
                $this->assertNull($model->email);
            }
        }
    }

    public function test_registered_card_optional_details_accept_missing_and_null_values(): void
    {
        foreach ([CustomerCardCreatingResponse::class, CustomerCardRetrievingResponse::class, CustomerCardUpdatingResponse::class] as $class) {
            foreach ([[], ['expire' => null, 'holderName' => null]] as $optional) {
                $model = (new FinModelBinding)->card(new $class([
                    'id' => 'c123456789012345678901234',
                    'customerId' => 'customer123',
                    'defaultFlag' => '0',
                    'cardNo' => '************1111',
                    'cardNoHash' => str_repeat('a', 64),
                    ...$optional,
                ]));
                $this->assertFalse($model->default_flag);
                $this->assertNull($model->holder_name);
                $this->assertNull($model->expire);
            }
        }
    }

    public function test_union_responses_used_by_the_payment_client_are_saved(): void
    {
        foreach ([
            CreatePayment200Response::class => PaymentStatus::UNPROCESSED,
            RetrievePayment200Response::class => PaymentStatus::AUTHORIZED,
            CapturePayment200Response::class => PaymentStatus::CAPTURED,
        ] as $class => $status) {
            $values = (array) (new PaymentCardCreatingResponse([
                ...$this->paymentValues(),
                'status' => $status,
            ]))->jsonSerialize();
            $response = ObjectSerializer::deserialize(json_encode($values), $class);

            $payment = (new FinModelBinding)->payment($response)->fresh();

            $this->assertSame($status, $payment->status);
            $this->assertNull($payment->pay_method->method);
        }
        $this->assertSame(1, FinPaymentCard::count());
    }

    private function paymentValues(): array
    {
        return [
            'shopId' => 's123456789012',
            'id' => 'binding-test-order',
            'accessId' => '123456789012345678901234',
            'amount' => 100,
            'tax' => 0,
            'totalAmount' => 100,
            'payType' => PayType::CARD,
            'jobCode' => CardPaymentJobCode::AUTH,
            'status' => PaymentStatus::UNPROCESSED,
            'processDate' => '2026/11/02 10:00:00.000',
            'created' => '2026/11/02 10:00:00.000',
        ];
    }
}
