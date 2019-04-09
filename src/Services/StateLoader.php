<?php

namespace App\Services;

use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

class StateLoader
{
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(State::class);
    }

    public function load(string $name): ?State
    {
        return $this->repository->findOneBy([
            'name' => $name,
        ]);
    }
}
