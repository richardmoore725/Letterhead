<?php

namespace App\Http\Controllers;

use App\Collections\UserCollection;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\LetterServiceInterface;
use App\Http\Services\MailChimpFacadeInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\Letter;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

class LetterController extends Controller
{
    private $letterService;
    private $channelService;
    private $userService;

    public function __construct(
        LetterServiceInterface $letterService,
        ChannelServiceInterface $channelService,
        UserServiceInterface $userService
    ) {
        $this->letterService  = $letterService;
        $this->channelService = $channelService;
        $this->userService = $userService;
    }

    public function createLetter(array $authors, Letter $letter, array $letterParts): JsonResponse
    {
        $updatedLetter = $this->letterService->createLetter($authors, $letterParts, $letter);

        if (empty($updatedLetter)) {
            return response()->json('We were not able to create this letter', 500);
        }

        return response()->json($updatedLetter->convertToArray(), 201);
    }

    public function deleteLetterById(Request $request, int $letterId): JsonResponse
    {
        $letter = $this->letterService->getLetterById($letterId);

        if (empty($letter)) {
            return response()->json('We couldn\'t find this letter.', 404);
        }

        $hasLetterBeenDeleted = $this->letterService->deleteLetter($letter);

        return response()->json($hasLetterBeenDeleted, $hasLetterBeenDeleted ? 200 : 500);
    }

    public function getAuthorsByChannel(UserServiceInterface $userService, Cache $cache, Channel $channel): JsonResponse
    {
        $cacheAuthorsKey = "channel_{$channel->getId()}_authors";

        /**
         * @var UserCollection
         */
        $authorsFromCache = $cache->get($cacheAuthorsKey);

        if (!empty($authorsFromCache)) {
            return response()->json($authorsFromCache->getPublicArray());
        }

        $authors = $userService->getBrandAdministrators($channel->getBrandId());

        $cacheExpiration = CarbonImmutable::now()->addDay()->toDateTime();
        $cache->put($cacheAuthorsKey, $authors, $cacheExpiration);

        return response()->json($authors->getPublicArray());
    }

    /**
     * The Letter is actually detected and loaded by our Middleware,
     * @param Letter $letter
     * @return JsonResponse
     */
    public function getLetterById(Letter $letter): JsonResponse
    {
        return response()->json($letter->convertToArray());
    }

    public function getLettersByChannelId(Channel $channel): JsonResponse
    {
        $letters = $this->letterService->getLettersByChannelId($channel->getId());
        return response()->json($letters->getPublicArray());
    }

    public function updateLetter(int $letterId, array $authors, array $letterParts, Letter $letter, Letter $existingLetter): JsonResponse
    {
        if (
            $letter->getPublicationStatus() === Letter::PUBLICATION_STATUS_REVIEW
            && $existingLetter->getPublicationStatus() === Letter::PUBLICATION_STATUS_REVIEW
        ) {
            return response()->json($existingLetter->convertToArray());
        }

        if ($letter->getPublicationStatus() === Letter::PUBLICATION_STATUS_SCHEDULED) {
            $letter->setEmailTemplate($existingLetter->getEmailTemplate());
        }

        $updatedLetter = $this->letterService->updateLetter($letterId, $authors, $letterParts, $letter);

        if (empty($updatedLetter)) {
            return response()->json('We failed to update the letter', 500);
        }

        return response()->json($updatedLetter->convertToArray());
    }

    public function sendLetterTestEmail(
        MailChimpFacadeInterface $mailChimp,
        Request $request,
        Letter $letter,
        Channel $channel
    ): JsonResponse {
        $toEmail = $request->input('toEmail');

        if (empty($toEmail)) {
            return response()->json("toEmail is required", 400);
        }

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json("{$toEmail} is not a valid email address", 400);
        }

        $emailInfo = $this->getChannelEmailInfo($channel);
        $subject = $letter->getTitle();
        $arrayOfUserFullNames = $this->getArrayOfUserFullNamesFromLetter($letter);

        $authorNamesString = empty($arrayOfUserFullNames) ? 'Unknown author'
            : $this->getUserNamesInOneString($arrayOfUserFullNames);

        $letterBanner = empty($letter->getSpecialBanner())
                        ? $channel->getChannelHorizontalLogo()
                        : $letter->getSpecialBanner();

        $htmlContents = view('newsletter', [
            'newsletter' => $letter,
            'authors' => $authorNamesString,
            'channel' => $channel,
            'banner' => $letterBanner
            ])->render();

        $response = $mailChimp->sendTestEmail($toEmail, $emailInfo->fromEmail, $emailInfo->fromName, $subject, $htmlContents);

        return $response->isSuccess()
            ? response()->json("Success", 200)
            : response()->json($response->getEndUserMessage(), $response->getStatus());
    }

    private function getArrayOfUserFullNamesFromLetter(Letter $letter): array
    {
        $arrayOfUserIds = $letter->getAuthors()->getPublicArray();
        $users = $this->userService->getUsersByUserIds($arrayOfUserIds);

        return $users->getArrayOfUserFullNames();
    }

    private function getUserNamesInOneString(array $arrayOfUserFullNames): string
    {
        if (sizeof($arrayOfUserFullNames) === 1) {
            return implode('|', $arrayOfUserFullNames);
        }

        if (sizeof($arrayOfUserFullNames) === 2) {
            return implode(' and ', $arrayOfUserFullNames);
        }

        $arrayOfNamesJoinWithComas = array_slice($arrayOfUserFullNames, 0, sizeof($arrayOfUserFullNames) - 1);
        $stringOfNamesJoinWithComas = implode(', ', $arrayOfNamesJoinWithComas);

        return $stringOfNamesJoinWithComas . ' and ' . array_pop($arrayOfUserFullNames);
    }

    /**
     * @param Letter $letter
     * @param Channel $channel
     * @return JsonResponse
     * @throws \Throwable
     * @todo most of this should be moved into a service and abstracted.
     */
    public function sendNewsletterEmail(Letter $letter, Channel $channel, MailChimpFacadeInterface $mailChimp): JsonResponse
    {
        $arrayOfUserFullNames = $this->getArrayOfUserFullNamesFromLetter($letter);

        $authorNamesString = empty($arrayOfUserFullNames) ? 'Unknown author'
            : $this->getUserNamesInOneString($arrayOfUserFullNames);

        $pixel = $this->letterService->getLetterTrackingPixel($letter);

        $letterBanner = empty($letter->getSpecialBanner())
                        ? $channel->getChannelHorizontalLogo()
                        : $letter->getSpecialBanner();

        $htmlContents = view('newsletter', [
            'newsletter' => $letter,
            'channel' => $channel,
            'authors' => $authorNamesString,
            'insertPixel' => true,
            'pixel' => $pixel,
            'banner' => $letterBanner
        ])->render();

        $subject = $letter->getTitle();

        $response = $mailChimp->sendEmailForChannel($letter, $channel, $subject, $htmlContents);

        if ($response->isSuccess()) {
            $this->letterService->markLetterAsPublished($letter);
        }

        return $response->isSuccess()
            ? response()->json("Success", 200)
            : response()->json($response->getEndUserMessage(), $response->getStatus());
    }

    private function getChannelEmailInfo(Channel $channel): \stdClass
    {
        $channelConfigurations = $channel->getChannelConfigurations();

        return (object)[
            'fromEmail' => $channelConfigurations->getDefaultFromEmailAddress(),
            'fromName' => $channelConfigurations->getDefaultEmailFromName()
        ];
    }

    public function uploadImages(Request $request, int $brandId, int $channelId): JsonResponse
    {
        $channelSpacesPath = "platformservice/brands/{$brandId}/channels/{$channelId}";
        $currentTime = time();

        $image = $request->file('letterImage');

        $imagePath = empty($image)
            ? ''
            : $image->storePubliclyAs("{$channelSpacesPath}", "channelId-{$channelId}-{$currentTime}-image.{$image->extension()}", 'spaces');

        $imageUrl = Storage::url($imagePath);

        return empty($imageUrl)
            ? response()->json("We can't get the image url", 500)
            : response()->json($imageUrl, 201);
    }

    /**
     * Send a test email with a specific letter.
     *
     * @param AdServiceInterface $promotionService
     * @param Request $request
     * @param Channel $channel
     * @param Letter $letter
     * @return JsonResponse
     */
    public function test(AdServiceInterface $promotionService, Request $request, Channel $channel, Letter $letter): JsonResponse
    {
        /**
         * The Delta is a JSON-encoded object that originates from our client-side
         * Authoring editor.
         *
         * @var String
         */
        $delta = $request->input('delta', '');

        /**
         * The `toEmail` is an email address.
         *
         * @var String
         */
        $email = $request->input('toEmail', '');

        if (empty($delta) || empty($email)) {
            return response()->json('Remember to send an email address in addition to the delta', 400);
        }


        /**
         * Promotions are an optional array we may post to the Letter in order to
         * generate its template. Their source of truth, however, is not the
         * letter.
         *
         * @var array
         */
        $promotions = $promotionService->getPromotionsFromFromJsonString($request->input('promotions', ''));

        $arrayOfUserIds = $letter->getAuthors()->getPublicArray();
        $users = $this->userService->getUsersByUserIds($arrayOfUserIds);
        $template = $this->letterService->generateLetterEmailTemplate($users, $channel, $delta, $letter, $promotions);

        if ($template->isError()) {
            return $template->getJsonResponse();
        }

        $fromEmailAddress = $channel->getChannelConfigurations()->getDefaultFromEmailAddress();
        $fromName = $channel->getChannelConfigurations()->getDefaultEmailFromName();
        $subject = $letter->getTitle();

        $testEmailResponse = $this->letterService->test($channel, $email, $letter->getEmailServiceProvider(), $letter, $fromEmailAddress, $fromName, $subject, $template->getData());

        return $testEmailResponse->getJsonResponse();
    }
}
