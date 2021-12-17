<?php

namespace App\Http\Middleware;

use App\Http\Services\BrandServiceInterface;
use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;

class VerifyBrandDoesntExistMiddleware
{
    private $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $brandSlugFromRequest = $request->input('brandSlug', '');
        $brand = $this->brandService->getBrandBySlug($brandSlugFromRequest);

        if (!empty($brand)) {
            return response()->json("Unforunately, a brand with this slug already exists", 409);
        }

        return $next($request);
    }
}
