<?php

namespace App\Http\Repositories;

use App\Http\Response;

interface MjmlTemplateRepositoryInterface
{
    public function getHtmlFromMjml(string $mjml): Response;
}
