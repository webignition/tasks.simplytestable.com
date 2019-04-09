<?php

namespace App\Services;

use App\Entity\Task;
use Hashids\Hashids;

class TaskIdentifierFactory
{
    private $instanceId;
    private $hashIdCreator;

    public function __construct(int $instanceId, Hashids $hashIdCreator)
    {
        $this->instanceId = $instanceId;
        $this->hashIdCreator = $hashIdCreator;
    }

    public function create(Task $task)
    {
        return $this->hashIdCreator->encode([
            $this->instanceId,
            $task->getId(),
        ]);
    }
}
