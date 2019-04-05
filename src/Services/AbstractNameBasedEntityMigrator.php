<?php

namespace App\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class AbstractNameBasedEntityMigrator
{
    private $resourceLoader;
    private $entityManager;

    /**
     * @var EntityRepository|ObjectRepository
     */
    private $repository;

    public function __construct(
        YamlResourceLoader $resourceLoader,
        EntityManagerInterface $entityManager,
        string $entityClass
    ) {
        $this->resourceLoader = $resourceLoader;
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($entityClass);
    }

    abstract protected function createEntity(string $name);

    public function migrate()
    {
        $flushRequired = false;

        $names = $this->resourceLoader->getData();

        foreach ($names as $name) {
            $entity = $this->repository->findOneBy([
                'name' => $name,
            ]);

            if (!$entity) {
                $entity = $this->createEntity($name);
                $this->entityManager->persist($entity);
                $flushRequired = true;
            }
        }

        if ($flushRequired) {
            $this->entityManager->flush();
        }
    }
}
