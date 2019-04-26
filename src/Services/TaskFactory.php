<?php

namespace App\Services;

use App\Entity\Task;
use App\Entity\TaskType;
use Doctrine\ORM\EntityManagerInterface;

class TaskFactory
{
    const CREATION_STATE = Task::STATE_PREFIX . Task::STATE_NEW;

    private $taskIdentifierFactory;
    private $stateLoader;
    private $entityManager;
    private $urlFactory;
    private $repository;

    public function __construct(
        TaskIdentifierFactory $taskIdentifierFactory,
        StateLoader $stateLoader,
        EntityManagerInterface $entityManager,
        UrlFactory $urlFactory
    ) {
        $this->taskIdentifierFactory = $taskIdentifierFactory;
        $this->stateLoader = $stateLoader;
        $this->entityManager = $entityManager;
        $this->urlFactory = $urlFactory;
        $this->repository = $entityManager->getRepository(Task::class);
    }

    public function create(
        string $jobIdentifier,
        string $urlString,
        TaskType $type,
        string $parameters
    ): Task {
        $url = $this->urlFactory->create($urlString);

        $state = $this->stateLoader->load(self::CREATION_STATE);

        $task = Task::create($jobIdentifier, $url, $state, $type, $parameters);

        $existingTask = $this->findExistingTask($task);

        if ($existingTask instanceof Task) {
            $task = $existingTask;
        } else {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $task->setIdentifier($this->taskIdentifierFactory->create($task));

            $this->entityManager->persist($task);
            $this->entityManager->flush();
        }

        return $task;
    }

    private function findExistingTask(Task $task): ?Task
    {
        $existingTask = $this->repository->findOneBy([
            'jobIdentifier' => $task->getJobIdentifier(),
            'url' => $task->getUrl(),
            'type' => $task->getType(),
        ]);

        return $existingTask instanceof Task
            ? $existingTask
            : null;
    }
}
