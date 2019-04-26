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

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $task->setIdentifier($this->taskIdentifierFactory->create($task));

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }
}
