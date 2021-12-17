<?php

namespace App\Collections;

use App\DTOs\TransactionalEmailDto;
use App\Models\TransactionalEmail;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class TransactionalEmailCollection extends BaseCollection
{
    public function __construct(Collection $transactionalEmailDatabaseResults)
    {
        $dtos = $this->getDtos($transactionalEmailDatabaseResults);
        $transactionalEmails = $this->getModels($dtos);

        parent::__construct($transactionalEmails);
    }

    private function getDtos(Collection $transactionalEmailDatabaseResults): array
    {
        return array_map(function ($transactionalEmailObject) {
            return new TransactionalEmailDto($transactionalEmailObject);
        }, $transactionalEmailDatabaseResults->toArray());
    }

    private function getModels(array $transactionalEmailDtos): array
    {
        return array_map(function (transactionalEmailDto $dto) {
            return new TransactionalEmail($dto);
        }, $transactionalEmailDtos);
    }
}
