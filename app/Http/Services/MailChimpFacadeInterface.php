<?php

namespace App\Http\Services;

use App\DTOs\MailChimpListDto;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\MailChimpList;
use DrewM\MailChimp\MailChimp;
use App\Collections\ListCollection;
use Illuminate\Support\Collection;

interface MailChimpFacadeInterface
{
    public function sendTestEmail(string $to, string $from, string $fromName, string $subject, string $htmlContents): Response;
    public function sendEmailForChannel(Letter $letter, Channel $channel, string $subject, string $htmlContents): Response;
    public function sendCampaign(string $campaignId): bool;
    public function setCampaignContent(string $campaignId, string $htmlContent): bool;
    public function createRegularCampaignForList(string $listId, int $segmentId, string $subject, string $replyTo, string $fromName): ?string;
    public function createRegularCampaignWithoutList(string $subject, string $replyTo, string $fromName): ?string;
    public function getListById(string $mailChimpListId): ?MailChimpList;
    public function getLists(): ListCollection;
    public function ping(): Response;
}
