<?php

namespace App\Services;

use App\Entity\State;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class StateMigrator
{
    private $stateNames;
    private $entityManager;

    /**
     * @var EntityRepository|ObjectRepository
     */
    private $repository;

    public function __construct(StateNames $stateNames, EntityManagerInterface $entityManager)
    {
        $this->stateNames = $stateNames;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(State::class);
    }

    public function migrate()
    {
        $flushRequired = false;

        $stateNames = $this->stateNames->getNames();

        foreach ($stateNames as $stateName) {
            $state = $this->repository->findOneBy([
                'name' => $stateName,
            ]);

            if (!$state) {
                $state = State::create($stateName);
                $this->entityManager->persist($state);
                $flushRequired = true;
            }
        }

        if ($flushRequired) {
            $this->entityManager->flush();
        }
    }
}
