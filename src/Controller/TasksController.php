<?php

namespace App\Controller;

use App\Entity\TaskType;
use App\Request\CreateTaskCollectionRequest;
use App\Services\TaskFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TasksController
{
    public function createAction(
        CreateTaskCollectionRequest $createTaskCollectionRequest,
        TaskFactory $taskFactory
    ): JsonResponse {
        if (!$createTaskCollectionRequest->isValid()) {
            throw new BadRequestHttpException();
        }

        $tasks = [];

        foreach ($createTaskCollectionRequest->getCreateTaskRequests() as $createTaskRequest) {
            $type = $createTaskRequest->getType();

            if ($type instanceof TaskType) {
                $task = $taskFactory->create(
                    $createTaskRequest->getJobIdentifier(),
                    (string) $createTaskRequest->getUri(),
                    $type,
                    $createTaskRequest->getParameters()
                );

                $tasks[] = $task;
            }
        }

        return new JsonResponse($tasks);
    }
}
