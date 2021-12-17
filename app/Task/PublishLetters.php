<?php

namespace App\Task;

use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\LetterServiceInterface;
use App\Jobs\SendEmailThroughMailChimpJob;
use App\Jobs\SendLetterThroughEmailServiceProviderJob;
use App\Models\Letter;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Queue;

class PublishLetters
{
    private $letterService;
    private $channelService;

    /**
     * @var Queue
     */
    private $queue;

    public function __construct(
        LetterServiceInterface $letterService,
        ChannelServiceInterface $channelService,
        Queue $queue
    ) {
        $this->letterService = $letterService;
        $this->channelService = $channelService;
        $this->queue = $queue;
    }

    public function __invoke()
    {
        /**
         * Get only Letters that are publishable at this moment.
         */
        $letters = $this->letterService->getPublishableLetters();

        /**
         * For each Publishable Letter, we'll confirm that that Letter's publication time is the same as the present
         * server time. If it is, we'll queue a Job to send the letter.
         */
        array_map(function (Letter $letter) {
            $letterPublicationDateTime = CarbonImmutable::parse($letter->getPublicationDate(), $letter->getPublicationDateOffset());
            $serverTimeWithLetterOffset = CarbonImmutable::now($letter->getPublicationDateOffset());

            if (!$letterPublicationDateTime->isSameMinute($serverTimeWithLetterOffset)) {
                return;
            }

            $email = new SendLetterThroughEmailServiceProviderJob($letter);

            $this->queue->pushOn('send_email', $email);
        }, $letters->getModelArray());
    }
}
