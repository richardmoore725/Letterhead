<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use Illuminate\Http\Request;
use Closure;

class ValidateBrandDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];

        $validator = app('validator')
            ->make($request->input(), Brand::getValidationRules());


        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $brandHorizontalLogo = $request->hasFile('brandHorizontalLogo') ? $request->file('brandHorizontalLogo') : null;
        $brandName = $request->input('brandName');
        $brandSlug = $request->input('brandSlug');
        $brandSquareLogo = $request->hasFile('brandSquareLogo') ? $request->file('brandSquareLogo') : null;


        $request->setRouteResolver(function () use ($brandHorizontalLogo, $brandName, $brandSlug, $brandSquareLogo, $route) {
            $route[2]['brandHorizontalLogo'] = $brandHorizontalLogo;
            $route[2]['brandName'] = $brandName;
            $route[2]['brandSlug'] = $brandSlug;
            $route[2]['brandSquareLogo'] = $brandSquareLogo;

            return $route;
        });

        return $next($request);
    }
}
