<?php

namespace App\Jobs;

use App\Http\Services\UserServiceInterface;
use App\Models\PassportStamp;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class ScaffoldPermissionsJob extends Job
{
    private $model;
    private $resourceId;

    public function __construct(
        string $model,
        int $resourceId
    ) {
        $this->model = $model;
        $this->resourceId = $resourceId;
    }

    public function handle(UserServiceInterface $userService): void
    {
        $userService->createScaffoldResource($this->model, $this->resourceId);
    }
}
