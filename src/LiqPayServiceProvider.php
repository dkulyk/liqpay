<?php

namespace DKulyk\LiqPay;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class LiqPayServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     */
    public function register()
    {
        $this->app->singleton(
            'liqpay',
            function (ApplicationContract $app) {
                $config = $app->make('config');

                return new LiqPay($config->get('liqpay'));
            }
        );

        $this->app->singleton(
            'command.liqpay.database',
            function (ApplicationContract $app) {
                return new LiqPayTableCommand($app->make('files'));
            }
        );

        $this->commands('command.liqpay.database');

        $this->map($this->app->make('router'));
    }

    /**
     * Define the routes for the application.
     *
     * @param  Router $router
     */
    public function map(Router $router)
    {
        $router->any('liqpay', ['uses' => 'LiqPay\LiqPayController@callback', 'as' => 'liqpay.callback']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['command.liqpay.database', 'liqpay'];
    }
}