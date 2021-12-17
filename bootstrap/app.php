<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton('filesystem', function ($app) {
    return $app->loadComponent(
        'filesystems',
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        'filesystem'
    );
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

/**
 * Middleware that must be manually applied to a given route.
 */
$app->routeMiddleware([
    'apiKeyMiddleware' => \App\Http\Middleware\ApiKeyMiddleware::class,
    'authorize' => \App\Http\Middleware\AuthorizeUserMiddleware::class,
    'authorizeUserAction' => \App\Http\Middleware\AuthorizeUserActionMiddleware::class,
    'passport' => \App\Http\Middleware\PassportMiddleware::class,
    'brandApiKey' => \App\Http\Middleware\BrandApiKeyMiddleware::class,
    'validateLetter' => \App\Http\Middleware\ValidateLetterMiddleware::class,
    'validateBrand' => \App\Http\Middleware\ValidateBrandDataMiddleware::class,
    'validateChannel' => \App\Http\Middleware\ValidateChannelDataMiddleware::class,
    'verifyBrand' => \App\Http\Middleware\VerifyBrandMiddleware::class,
    'verifyBrandDoesntExist' => \App\Http\Middleware\VerifyBrandDoesntExistMiddleware::class,
    'verifyChannel' => \App\Http\Middleware\VerifyChannelMiddleware::class,
    'verifyChannelDoesntExist' => \App\Http\Middleware\VerifyChannelDoesntExistMiddleware::class,
    'verifyConfiguration' => \App\Http\Middleware\VerifyConfigurationMiddleware::class,
    'verifyLetter' => \App\Http\Middleware\VerifyLetterMiddleware::class,
    'servicePlatformKey' => \App\Http\Middleware\ServicePlatformKeyMiddleware::class,
    'validatePlatformEvent' => \App\Http\Middleware\ValidatePlatformEventDataMiddleware::class,
    'verifyPlatformEvent' => \App\Http\Middleware\VerifyPlatformEventMiddleware::class,
    'validateEmail' => \App\Http\Middleware\ValidateEmailDataMiddleware::class,
    'verifyEmail' => \App\Http\Middleware\VerifyEmailMiddleware::class,
    'validateDiscountCode' => \App\Http\Middleware\ValidateDiscountCodeDataMiddleware::class,
    'verifyDiscountCode' => \App\Http\Middleware\VerifyDiscountCodeMiddleware::class,
    'verifyMailChimp' => \App\Http\Middleware\VerifyMailChimpMiddleware::class,
    'verifyConstantContact' => \App\Http\Middleware\VerifyConstantContactMiddleware::class,
    'verifyPromotionOrder' => \App\Http\Middleware\VerifyPromotionOrderMiddleware::class,
    'validateTransactional' => \App\Http\Middleware\ValidateTransactionalEmailDataMiddleware::class,
    'verifyTransactional' => \App\Http\Middleware\VerifyTransactionalEmailMiddleware::class,
    'verifyAggregate' => \App\Http\Middleware\VerifyAggregateMiddleware::class,
    'validateAggregate' => \App\Http\Middleware\ValidateAggregateDateMiddleware::class,
    'validatePromotionMessage' => \App\Http\Middleware\ValidatePromotionMessageDataMiddleware::class,
    'verifyPromotion' => \App\Http\Middleware\VerifyPromotionMiddleware::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers and Configurations
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
*/

/**
 * The AppServiceProvider is where we mainly wire together interfaces and their classes.
 */
$app->register(App\Providers\AppServiceProvider::class);

/**
 * LumenGeneratorServiceProvider re-adds a variety of `artisan` commands that are normally native to
 * Laravel but excluded from Lumen. These are utilities, and you can see - after this class has been
 * attached - which are provided by typing `php artisan` in your console.
 */
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);

/**
 * The RedisServiceProvider helps connect our app to our Redis database. We use Redis to
 * manage our cache, jobs, and the like in production.
 */
$app->register(\Illuminate\Redis\RedisServiceProvider::class);

/**
 * RollbarServiceProvider makes it easier for us to integrate our logging tool, Rollbar, into
 * Platform Service.
 */
$app->register(\App\Providers\RollbarServiceProvider::class);

/**
 * EventServiceProvider takes care of all of our events and their listeners
 */
$app->register(\App\Providers\EventServiceProvider::class);

/*
 * Lumen provides a clean, simple API over the popular SwiftMailer library
 * with drivers for SMTP, Mailgun, SparkPost, Amazon SES, PHP's mail function, and sendmail,
 * allowing you to quickly get started sending mail through a local or cloud based service of your choice.
*/
$app->register(Illuminate\Mail\MailServiceProvider::class);

$app->configure('database');

$app->configure('mail');

$app->configure('filesystems');

/**
 * The Services (config/services.php) configuration settins are where we can
 * stash connection information about third-party services, such as Amazon SES.
 */
$app->configure('services');

$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);

$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);

/**
 * Configurations
 *
 * Where we have custom configuration options in the /config directory,
 * we need to `configure` them, which informs our app of their
 * existence.
 */

/**
 * The `logging` configuration allows us to set basic options for logging Lumen and general
 * PHP errors to Rollbar.
 *
 * @uses \App\Providers\RollbarServiceProvider
 */
$app->configure('logging');

/**
 * Configures our config/queue.php with the service container. This is
 * responsible for describing our queues, which are used to
 * run processes asynchronously. How neat 🎉.
 */
$app->configure('queue');


/**
 * @uses
 */
$app->configure('scribe');

$app->configure('mail');

$app->configure('filesystems');

$app->configure('queue');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/api.php';
    require __DIR__.'/../routes/feeds.php';
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/emailRoutes.php';
    require __DIR__.'/../routes/transactionalEmailRoutes.php';

    /**
     * Our v2 API endpoints.
     */
    require __DIR__.'/../routes/api/v2/root.php';
});

return $app;
