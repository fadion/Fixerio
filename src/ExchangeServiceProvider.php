<?php

namespace Fadion\Fixerio;

use Illuminate\Support\ServiceProvider;

class ExchangeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Exchange::class, function ($app) {
            return (new Exchange)->key(config("services.fixer.key"));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Exchange::class];
    }

}
