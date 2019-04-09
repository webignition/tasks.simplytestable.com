<?php

namespace App\Services;

use App\Entity\TaskType;
use Doctrine\ORM\EntityManagerInterface;

class TaskTypeLoader
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(TaskType::class);
    }

    public function load(string $name): ?TaskType
    {
        return $this->repository->findOneBy([
            'name' => $name,
        ]);
    }
}
