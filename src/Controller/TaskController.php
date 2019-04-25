<?php

namespace App\Controller;

use App\Entity\TaskType;
use App\Request\CreateTaskRequest;
use App\Services\TaskFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TaskController
{
    public function createAction(CreateTaskRequest $createTaskRequest, TaskFactory $taskFactory)
    {
        $type = $createTaskRequest->getType();

        if (!$createTaskRequest->isValid() || !$type instanceof TaskType) {
            throw new BadRequestHttpException();
        }

        $task = $taskFactory->create(
            $createTaskRequest->getJobIdentifier(),
            (string) $createTaskRequest->getUri(),
            $type,
            $createTaskRequest->getParameters()
        );

        return new JsonResponse($task);
    }
}
