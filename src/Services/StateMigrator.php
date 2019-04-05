<?php

namespace App\Services;

use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

class StateMigrator extends AbstractNameBasedEntityMigrator
{
    public function __construct(YamlResourceLoader $resourceLoader, EntityManagerInterface $entityManager)
    {
        parent::__construct($resourceLoader, $entityManager, State::class);
    }

    protected function createEntity(string $name)
    {
        return State::create($name);
    }
}
