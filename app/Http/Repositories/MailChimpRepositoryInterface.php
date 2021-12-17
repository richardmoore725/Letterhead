<?php

namespace App\Http\Repositories;

use App\DTOs\MailChimpListDto;
use App\Collections\ListCollection;
use App\Collections\SegmentCollection;
use DrewM\MailChimp\MailChimp;
use App\Http\Response;

interface MailChimpRepositoryInterface extends EspRepositoryInterface
{
    public function getListById(MailChimp $mailChimp, string $mailChimpListId): ?MailChimpListDto;
    public function getLists(MailChimp $mailChimp): ListCollection;
    public function getListSegments(MailChimp $mailChimp, string $mailChimpListId): SegmentCollection;
    public function ping(MailChimp $mailChimp): Response;
}
