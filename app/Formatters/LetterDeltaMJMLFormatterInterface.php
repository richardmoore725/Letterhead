<?php

namespace App\Formatters;

use App\Collections\UserCollection;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Letter;

interface LetterDeltaMJMLFormatterInterface
{
    public function renderMjmlTemplate(UserCollection $authors, Channel $channel, string $delta, Letter $letter, array $promotions): Response;
}
