<?php

namespace App\Http\Middleware;

use App\Http\Services\BrandServiceInterface;
use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;

class ValidateChannelDataMiddleware
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
        $route = $request->route();
        $routeParameters = $route[2];

        $validator = app('validator')
            ->make($request->input(), Channel::getValidationRules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $accentColor = $request->input('accentColor', '#0000EE');
        $channelSlug = $request->input('channelSlug');
        $channelDescription = $request->input('channelDescription');

        $channelImage = $request->hasFile('channelImage')
            ? $request->file('channelImage')
            : (($this->isAvailableString($request->input('channelImage')))
                ? $request->input('channelImage')
                : ''
              );

        $channelHorizontalLogo = $request->hasFile('channelHorizontalLogo')
              ? $request->file('channelHorizontalLogo')
              : (($this->isAvailableString($request->input('channelHorizontalLogo')))
                  ? $request->input('channelHorizontalLogo')
                  : ''
                );

        $channelSquareLogo = $request->hasFile('channelSquareLogo')
            ? $request->file('channelSquareLogo')
            : (($this->isAvailableString($request->input('channelSquareLogo')))
                ? $request->input('channelSquareLogo')
                : ''
              );

        $defaultEmailFromName = $request->input('defaultEmailFromName', '');
        $defaultFromEmailAddress = $request->input('defaultFromEmailAddress', '');
        $defaultFont = $request->input('defaultFont', 'Montserrat');
        $enableChannelAuthoring = filter_var($request->input('enableChannelAuthoring', false), FILTER_VALIDATE_BOOLEAN);
        $headingFont = $request->input('headingFont', 'Montserrat');
        $loadPromosBeforeHeadings = filter_var($request->input('loadPromosBeforeHeadings', false), FILTER_VALIDATE_BOOLEAN);
        $title = $request->input('title');

        $request->setRouteResolver(function () use (
            $accentColor,
            $channelHorizontalLogo,
            $channelSlug,
            $channelSquareLogo,
            $channelDescription,
            $channelImage,
            $defaultEmailFromName,
            $defaultFromEmailAddress,
            $defaultFont,
            $enableChannelAuthoring,
            $headingFont,
            $loadPromosBeforeHeadings,
            $title,
            $route
        ) {
            $route[2]['accentColor'] = $accentColor;
            $route[2]['channelSlug'] = $channelSlug;
            $route[2]['channelDescription'] = $channelDescription;
            $route[2]['channelHorizontalLogo'] = $channelHorizontalLogo;
            $route[2]['channelSquareLogo'] = $channelSquareLogo;
            $route[2]['channelImage'] = $channelImage;
            $route[2]['defaultEmailFromName'] = $defaultEmailFromName;
            $route[2]['defaultFromEmailAddress'] = $defaultFromEmailAddress;
            $route[2]['defaultFont'] = $defaultFont;
            $route[2]['enableChannelAuthoring'] = $enableChannelAuthoring;
            $route[2]['headingFont'] = $headingFont;
            $route[2]['loadPromosBeforeHeadings'] = $loadPromosBeforeHeadings;
            $route[2]['title'] = $title;

            return $route;
        });

        return $next($request);
    }

    private function isAvailableString($stringValue): bool
    {
        if (!empty($stringValue) && $this->isNotNullString($stringValue)) {
            return true;
        }
        return false;
    }

    private function isNotNullString($stringValue): bool
    {
        if ($stringValue !== 'null') {
            return true;
        }
        return false;
    }
}
