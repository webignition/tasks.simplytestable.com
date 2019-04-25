<?php

namespace App\Services\FixtureLoader;

use App\Entity\State;
use App\Services\DataProviderInterface;
use Doctrine\ORM\EntityManagerInterface;

class StateFixtureLoader extends AbstractNameBasedFixtureLoader
{
    public function __construct(EntityManagerInterface $entityManager, DataProviderInterface $dataProvider)
    {
        parent::__construct($entityManager, $dataProvider);
    }

    protected function getEntityClass(): string
    {
        return State::class;
    }

    protected function createEntity(string $name)
    {
        return State::create($name);
    }
}
