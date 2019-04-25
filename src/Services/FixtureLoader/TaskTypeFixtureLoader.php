<?php

namespace App\Services\FixtureLoader;

use App\Entity\TaskType;
use App\Services\DataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskTypeFixtureLoader extends AbstractNameBasedFixtureLoader
{
    public function __construct(EntityManagerInterface $entityManager, DataProviderInterface $dataProvider)
    {
        parent::__construct($entityManager, $dataProvider);
    }

    protected function getEntityClass(): string
    {
        return TaskType::class;
    }

    protected function createEntity(string $name)
    {
        return TaskType::create($name);
    }
}
