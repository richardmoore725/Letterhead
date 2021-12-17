<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Brand;
use App\Http\Services\BrandServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    private $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return new Response('Oops. Remember to send your brand Api key as a bearer token.', 400);
        }

        $brandSlug = $request->route()[2]["brandSlug"];

        $brand = $this->brandService->getBrandBySlug($brandSlug);

        if (empty($brand)) {
            return new Response('We couldn\'t find this brand.', 404);
        }

        if ($token !== $this->brandService->getBrandApiKeyByBrandId($brand->getId())) {
            return new Response('Your brand Api key is unauthorized.', 403);
        }

        $request->request->set('brand', $brand);

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
