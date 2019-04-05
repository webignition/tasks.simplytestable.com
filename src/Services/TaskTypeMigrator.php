<?php

namespace App\Services;

use App\Entity\TaskType;
use Doctrine\ORM\EntityManagerInterface;

class TaskTypeMigrator extends AbstractNameBasedEntityMigrator
{
    public function __construct(YamlResourceLoader $resourceLoader, EntityManagerInterface $entityManager)
    {
        parent::__construct($resourceLoader, $entityManager, TaskType::class);
    }

    protected function createEntity(string $name)
    {
        return TaskType::create($name);
    }
}
