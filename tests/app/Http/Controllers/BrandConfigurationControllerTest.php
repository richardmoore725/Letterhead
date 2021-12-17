<?php

namespace App\Tests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BrandConfigurationController;
use App\Http\Services\BrandServiceInterface;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BrandConfigurationControllerTest extends TestCase
{
    private $controller;
    private $brandService;
    private $request;

    public function setup(): void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->controller = new BrandConfigurationController($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCanConnectBrandAccountToStripe()
    {
        $code = 'ca_Go8JMMchY9qTtEynlY0Sg2ahnpWNUrwN';
        $scope = 'scope';
        $state = '%7B%22brandId%22:14,%22stripeAccountConfigurationId%22:4,%22stripePublishableKeyConfigurationId%22:5,%22stripeAccessTokenConfigurationId%22:6,%22redirectUri%22:%22https://www.google.com%22%7D';

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('code')
            ->willReturn($code);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('state')
            ->willReturn($state);

        $this->brandService
            ->expects($this->once())
            ->method('updateBrandConfigurationWithStripeDto')
            ->with(
                'ca_Go8JMMchY9qTtEynlY0Sg2ahnpWNUrwN',
                14
            )
            ->willReturn(true);

        $actualResults = $this->controller->connectBrandAccountToStripe($this->request);
        $this->assertInstanceOf(RedirectResponse::class, $actualResults);
        $this->assertEquals(302, $actualResults->getStatusCode());
    }

    public function testCanDeauthorizedFromStripe_returns200()
    {
        $account = 'stripe_account';

        $this->request
        ->expects($this->once())
        ->method('input')
        ->with('account')
        ->willReturn($account);

        $this->brandService
        ->expects($this->once())
        ->method('updateBrandConfigurationByBrandConfigurationValue')
        ->with('stripe_account', '')
        ->willReturn(true);

        $actualResults = $this->controller->deauthorizedFromStripe($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
        $this->assertEquals('"You are disconnected from Stripe."', $actualResults->getContent());
    }

    public function testCannotDeauthorizedFromStripe_returns500()
    {
        $account = 'stripe_account';

        $this->request
        ->expects($this->once())
        ->method('input')
        ->with('account')
        ->willReturn($account);

        $this->brandService
        ->expects($this->once())
        ->method('updateBrandConfigurationByBrandConfigurationValue')
        ->with('stripe_account', '')
        ->willReturn(false);

        $actualResults = $this->controller->deauthorizedFromStripe($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
        $this->assertEquals('"Sorry we were unable to disconnect you from Stripe."', $actualResults->getContent());
    }
}
