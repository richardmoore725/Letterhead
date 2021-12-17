<?php

namespace App\Http\Middleware;

use App\Http\Services\LetterServiceInterface;
use App\Models\Email;
use App\Models\Letter;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;

class ValidateLetterMiddleware
{
    private $letterService;

    public function __construct(LetterServiceInterface $letterService)
    {
        $this->letterService = $letterService;
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
        $channel = isset($routeParameters['channel']) ? $routeParameters['channel'] : null;

        /**
         * ValidateLetterMiddleware is used to update a letter, too. In that case, assuming VerifyLetterMiddleware
         * comes before it, we should have a Letter object in the route.
         * @uses VerifyLetterMiddleware::handle()
         */
        $existingLetter = isset($routeParameters['letter']) ? $routeParameters['letter'] : null;

        /**
         * We require that there is a Channel set in the route parameters, which is
         * performed by VerifyChannelMiddleware.
         *
         * @uses VerifyChannelMiddleware::handle()
         */
        if (empty($channel)) {
            return response()->json('This middleware requires that VerifyChannelMiddleware is called first.', 500);
        }

        /**
         * Validate the raw POST request for the things we require.
         */
        $validationRules = Letter::getValidationRules();
        $validator = app('validator')->make($request->input(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        /**
         * We'll pull the properties we need from the Request object.
         */


        $authorsFromRequest = $request->input('authors', []);
        $authors = array_map(function ($authorId) {
            return (int) $authorId;
        }, $authorsFromRequest);
        $campaignId = $request->input('campaignId', '');
        $copyRendered = $request->input('copyRendered', '');
        $id = isset($route[2]['letterId']) ? $route[2]['letterId'] : null;
        $includePromotions = filter_var($request->input('includePromotions', false), FILTER_VALIDATE_BOOLEAN);
        $mjmlTemplate = $request->input('mjmlTemplate', '');
        $parts = $request->input('letterParts', []);
        $publicationDateFromRequest = $request->input('publicationDate');
        $publicationDate = $publicationDateFromRequest === "" ? $publicationDateFromRequest : CarbonImmutable::parse($publicationDateFromRequest)->toDateTimeString();
        $publicationStatus = $request->input('publicationStatus', Letter::PUBLICATION_STATUS_DRAFT);
        $segmentId = $request->input('segmentId');
        $slug = $request->input('slug', '');
        $subtitle = $request->input('subtitle');
        $specialBanner = $request->input('specialBanner', '');
        $title = $request->input('title');

        /**
         * If an `id` isn't present, we'll assume we're creating a _new_ Letter,
         * so we'll goahead and generate an empty object that we can pass around. Otherwise,
         * we'll try to find that Letter and instantiate it.
         */
        $emptyLetter = $this->letterService->generateEmptyLetter(
            $campaignId,
            $channel,
            $publicationDate,
            $publicationStatus,
            $includePromotions,
            $mjmlTemplate,
            $segmentId,
            $slug,
            $subtitle,
            $specialBanner,
            $title
        );

        $letterService = $this->letterService;
        $letterPartsFromRequest = array_map(function (array $part) use ($letterService) {
            /**
             * These parts are validated and enforced by the Letter's
             * validation rules.
             *
             * @see Letter::getValidationRules()
             */
            $copy = $part['copy'];
            $heading = isset($part['heading']) ? $part['heading'] : '';
            $id = isset($part['id']) ? (int) $part['id'] : null;

            return $letterService->generateEmptyLetterPart($copy, $heading, $id);
        }, $parts);

        $request->setRouteResolver(function () use (
            $authors,
            $copyRendered,
            $emptyLetter,
            $letterPartsFromRequest,
            $existingLetter,
            $id,
            $route
        ) {
            $route[2]['letter'] = $emptyLetter;
            $route[2]['authors'] = $authors;
            $route[2]['copyRendered'] = $copyRendered;
            $route[2]['letterId'] = $id;
            $route[2]['letterParts'] = $letterPartsFromRequest;
            $route[2]['existingLetter'] = $existingLetter;

            return $route;
        });

        return $next($request);
    }
}
