<?php

namespace App\Providers;

use App\Formatters\LetterDeltaMJMLFormatter;
use App\Formatters\LetterDeltaMJMLFormatterInterface;
use App\Http\Repositories\LetterRepository;
use App\Http\Repositories\LetterRepositoryInterface;
use App\Http\Repositories\MessageRepository;
use App\Http\Repositories\MessageRepositoryInterface;
use App\Http\Repositories\MjmlTemplateRepository;
use App\Http\Repositories\MjmlTemplateRepositoryInterface;
use App\Http\Repositories\PromotionRepository;
use App\Http\Repositories\PromotionRepositoryInterface;
use App\Http\Services\AdTypeService;
use App\Http\Services\AdTypeServiceInterface;
use App\Http\Services\LetterService;
use App\Http\Services\LetterServiceInterface;
use App\Http\Services\MessageService;
use App\Http\Services\MessageServiceInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * @uses LetterDeltaMJMLFormatterInterface
         * @uses LetterDeltaMJMLFormatter
         */
        $this->app->bind(
            LetterDeltaMJMLFormatterInterface::class,
            LetterDeltaMJMLFormatter::class
        );

        /**
         * We use the AdService to help shoulder some of the weight related to managing
         * ads through the AdService API.
         */
        $this->app->bind(
            'App\Http\Services\AdServiceInterface',
            'App\Http\Services\AdService'
        );

        /**
         * We use the AdTypeService and AdTypeServiceInterface to help shoulder some of
         * the weight related to processing AdType management.
         *
         * @uses AdTypeServiceInterface
         * @uses AdTypeService
         */
        $this->app->bind(
            'App\Http\Services\AdTypeServiceInterface',
            'App\Http\Services\AdTypeService'
        );

        /**
         * We use PackageService to shoulder the weight of package management!
         */
        $this->app->bind(
            'App\Http\Services\PackageServiceInterface',
            'App\Http\Services\PackageService'
        );

        $this->app->bind(
            'App\Http\Repositories\AuthRepositoryInterface',
            'App\Http\Repositories\AuthRepository'
        );

        $this->app->bind(
            'App\Http\Services\AuthServiceInterface',
            'App\Http\Services\AuthService'
        );

        $this->app->bind(
            'App\Http\Repositories\BeaconRepositoryInterface',
            'App\Http\Repositories\BeaconRepository'
        );

        $this->app->bind(
            'App\Http\Services\BeaconServiceInterface',
            'App\Http\Services\BeaconService'
        );

        /**
         * @uses LetterRepositoryInterface
         * @uses LetterRepository
         */
        $this->app->bind(
            'App\Http\Repositories\LetterRepositoryInterface',
            'App\Http\Repositories\LetterRepository'
        );

        /**
         * @uses LetterServiceInterface
         * @uses LetterService
         */
        $this->app->bind(
            'App\Http\Services\LetterServiceInterface',
            'App\Http\Services\LetterService'
        );

        /**
         * @uses MessageRepositoryInterface
         * @uses MessageRepository
         */
        $this->app->bind(
            'App\Http\Repositories\MessageRepositoryInterface',
            'App\Http\Repositories\MessageRepository'
        );

        /**
         * @uses MessageServiceInterface
         * @uses MessageService
         */
        $this->app->bind(
            'App\Http\Services\MessageServiceInterface',
            'App\Http\Services\MessageService'
        );

        /**
         * @uses MjmlTemplateRepositoryInterface
         * @uses MjmlTemplateRepository
         */
        $this->app->bind(
            'App\Http\Repositories\MjmlTemplateRepositoryInterface',
            'App\Http\Repositories\MjmlTemplateRepository'
        );

        $this->app->bind(
            'App\Http\Repositories\PlatformEventRepositoryInterface',
            'App\Http\Repositories\PlatformEventRepository'
        );

        $this->app->bind(
            'App\Http\Services\PlatformEventServiceInterface',
            'App\Http\Services\PlatformEventService'
        );

        $this->app->bind(
            PromotionRepositoryInterface::class,
            PromotionRepository::class
        );

        $this->app->bind(
            'App\Http\Repositories\EmailRepositoryInterface',
            'App\Http\Repositories\EmailRepository'
        );

        $this->app->bind(
            'App\Http\Services\EmailServiceInterface',
            'App\Http\Services\EmailService'
        );

        $this->app->bind(
            'App\Http\Repositories\DiscountCodeRepositoryInterface',
            'App\Http\Repositories\DiscountCodeRepository'
        );

        $this->app->bind(
            'App\Http\Services\DiscountCodeServiceInterface',
            'App\Http\Services\DiscountCodeService'
        );

        $this->app->bind(
            'App\Http\Repositories\BrandRepositoryInterface',
            'App\Http\Repositories\BrandRepository'
        );

        $this->app->bind(
            'App\Http\Services\BrandServiceInterface',
            'App\Http\Services\BrandService'
        );

        $this->app->bind(
            'App\Http\Services\ChannelServiceInterface',
            'App\Http\Services\ChannelService'
        );

        $this->app->bind(
            'App\Http\Services\MailChimpFacadeInterface',
            'App\Http\Services\MailChimpFacade'
        );

        $this->app->bind(
            'App\Http\Repositories\ConstantContactRepositoryInterface',
            'App\Http\Repositories\ConstantContactRepository'
        );

        $this->app->bind(
            'App\Http\Services\MailServiceInterface',
            'App\Http\Services\MailService'
        );

        $this->app->bind(
            'App\Http\Services\TransactionalEmailServiceInterface',
            'App\Http\Services\TransactionalEmailService'
        );

        /**
         * UserRespository is largely responsible for connecting with our external UserService, either
         * through API or other data sources.
         */
        $this->app->bind(
            'App\Http\Repositories\UserRepositoryInterface',
            'App\Http\Repositories\UserRepository'
        );

        /**
         * UserService helps us communicate back and forth with our UserService
         * beacon, as well as perform functions related to platform users.
         */
        $this->app->bind(
            'App\Http\Services\UserServiceInterface',
            'App\Http\Services\UserService'
        );

        $this->app->bind(
            'App\Http\Repositories\ChannelRepositoryInterface',
            'App\Http\Repositories\ChannelRepository'
        );

        $this->app->bind(
            'App\Http\Repositories\ConfigurationRepositoryInterface',
            'App\Http\Repositories\ConfigurationRepository'
        );


        $this->app->bind(
            'App\Http\Repositories\MailChimpRepositoryInterface',
            'App\Http\Repositories\MailChimpRepository'
        );

        $this->app->bind(
            'App\Http\Repositories\StripeRepositoryInterface',
            'App\Http\Repositories\StripeRepository'
        );

        $this->app->bind(
            'App\Http\Repositories\TransactionalEmailRepositoryInterface',
            'App\Http\Repositories\TransactionalEmailRepository'
        );

        $this->app->bind(
            'GuzzleHttp\ClientInterface',
            'GuzzleHttp\Client'
        );

        $this->app->bind(
            'App\Http\Services\SubscriberServiceInterface',
            'App\Http\Services\SubscriberService'
        );

        $this->app->bind(
            'App\Http\Repositories\LetterheadEspRepositoryInterface',
            'App\Http\Repositories\LetterheadEspRepository'
        );

        $this->app->bind(
            'App\Http\Repositories\AggregateRepositoryInterface',
            'App\Http\Repositories\AggregateRepository'
        );

        $this->app->bind(
            'App\Http\Services\AggregateServiceInterface',
            'App\Http\Services\AggregateService'
        );
    }
}
