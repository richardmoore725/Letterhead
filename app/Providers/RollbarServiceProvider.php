<?php

namespace App\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Rollbar\Rollbar;
use Rollbar\RollbarLogger;

class RollbarServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RollbarLogger::class, function () {
            $config = $this->app->make(Repository::class);

            $defaults = [
                'environment' => app()->environment(),
                'handle_error' => true,
                'handle_exception' => true,
                'handle_fatal' => true,
                'root' => base_path(),
            ];

            $rollbarConfiguration = array_merge($defaults, $config->get('logging.channels.rollbar', []));

            $handleError = (bool) array_pull($rollbarConfiguration, 'handle_error');
            $handleException = (bool) array_pull($rollbarConfiguration, 'handle_exception');
            $handleFatal = (bool) array_pull($rollbarConfiguration, 'handle_fatal');

            Rollbar::init($rollbarConfiguration, $handleException, $handleError, $handleFatal);

            return Rollbar::logger();
        });
    }

    /**
     * Boot the Rollbar services for the application.
     *
     * @return void
     * @see https://rollbar.com/wherebyus/newslettermicroservice/
     */
    public function boot()
    {
        Rollbar::init([
            'access_token' => env('LOG_ROLLBAR_TOKEN'),
            'environment' => env('APP_ENV'),
        ]);
    }
}
