<?php

namespace Fincode\Laravel;

use Fincode\Laravel\Console\Commands\PublishCommand;
use Fincode\Laravel\Models\FinCard;
use Fincode\Laravel\Models\FinCustomer;
use Fincode\Laravel\Models\FinPayment;
use Fincode\Laravel\Models\FinPaymentApplePay;
use Fincode\Laravel\Models\FinPaymentCard;
use Fincode\Laravel\Models\FinPaymentKonbini;
use Fincode\Laravel\Models\FinPlatform;
use Fincode\Laravel\Models\FinPlatformToken;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * @noinspection PhpUnused
 */
class LaravelFincodeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/fincode.php', 'fincode');
        }

        $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePublishes();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        Relation::enforceMorphMap([
            'fin_card' => FInCard::class,
            'fin_customer' => FinCustomer::class,
            'fin_payment_apple_pay' => FinPaymentApplePay::class,
            'fin_payment_card' => FinPaymentCard::class,
            'fin_payment_konbini' => FinPaymentKonbini::class,
            'fin_payment' => FinPayment::class,
            'fin_platform_token' => FinPlatformToken::class,
            'fin_platform' => FinPlatform::class,
        ]);
    }

    /**
     * Register the console commands for the package.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishCommand::class,
            ]);
        }
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishes(): void
    {
        if (app()->runningInConsole()) {
            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], ['fincode', 'fincode-migrations']);

            $this->publishes([
                __DIR__.'/../config/fincode.php' => config_path('fincode.php'),
            ], ['fincode', 'fincode-config']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [
            PublishCommand::class,
        ];
    }
}
