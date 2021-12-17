<?php

namespace App\Collections;

use App\DTOs\EmailDto;
use App\Models\Email;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class EmailCollection extends BaseCollection
{
    public function __construct(Collection $emailDatabaseResults)
    {
        $dtos = $this->getDtos($emailDatabaseResults);
        $emails = $this->getModels($dtos);

        parent::__construct($emails);
    }

    private function getDtos(Collection $emailDatabaseResults): array
    {
        return array_map(function ($emailObject) {
            return new EmailDto($emailObject);
        }, $emailDatabaseResults->toArray());
    }

    private function getModels(array $emailDtos): array
    {
        return array_map(function (emailDto $dto) {
            return new Email($dto);
        }, $emailDtos);
    }
}
