<?php

namespace App\Request;

class CreateTaskCollectionRequest
{
    const KEY_JOB_IDENTIFIER = 'job-identifier';
    const KEY_TASKS = 'tasks';

    private $jobIdentifier;

    /**
     * @var array|CreateTaskRequest[]
     */
    private $createTaskRequests;

    public function __construct(string $jobIdentifier, array $createTaskRequests)
    {
        $this->jobIdentifier = $jobIdentifier;

        foreach ($createTaskRequests as $createTaskRequest) {
            if ($createTaskRequest instanceof CreateTaskRequest && $createTaskRequest->isValid()) {
                $this->createTaskRequests[] = $createTaskRequest;
            }
        }
    }

    public function getJobIdentifier(): string
    {
        return $this->jobIdentifier;
    }

    /**
     * @return array|CreateTaskRequest[]
     */
    public function getCreateTaskRequests(): array
    {
        return $this->createTaskRequests;
    }

    public function isValid(): bool
    {
        if (empty($this->jobIdentifier)) {
            return false;
        }

        if (empty($this->createTaskRequests)) {
            return false;
        }

        return true;
    }
}
