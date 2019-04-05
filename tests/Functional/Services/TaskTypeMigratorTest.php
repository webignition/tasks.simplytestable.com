<?php

namespace App\Tests\Functional\Services;

use App\Entity\TaskType;
use App\Services\TaskTypeMigrator;
use App\Services\YamlResourceLoader;
use App\Tests\Functional\AbstractBaseTestCase;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TaskTypeMigratorTest extends AbstractBaseTestCase
{
    /**
     * @var TaskTypeMigrator
     */
    private $taskTypeMigrator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EntityRepository|ObjectRepository
     */
    private $repository;

    /**
     * @var string[]
     */
    private $taskTypeNames;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskTypeMigrator = self::$container->get(TaskTypeMigrator::class);
        $this->entityManager = self::$container->get(EntityManagerInterface::class);
        $this->repository = $this->entityManager->getRepository(TaskType::class);

        $states = $this->repository->findAll();

        foreach ($states as $state) {
            $this->entityManager->remove($state);
        }

        $this->entityManager->flush();

        $resourceLoader = self::$container->get('app.services.task_type_names');
        $this->taskTypeNames = $resourceLoader->getData();
    }

    public function testMigrateFromEmpty()
    {
        $this->assertEmpty($this->repository->findAll());

        $this->taskTypeMigrator->migrate();

        $this->assertTaskTypeNames($this->taskTypeNames, $this->getRepositoryTaskTypeNames());
    }

    public function testMigrateFromNonEmpty()
    {
        $expectedTaskTypeNames = $this->taskTypeNames;
        sort($expectedTaskTypeNames);

        $this->assertEmpty($this->repository->findAll());

        $taskTypeName = $expectedTaskTypeNames[0];

        $taskType = TaskType::create($taskTypeName);
        $this->entityManager->persist($taskType);
        $this->entityManager->flush();

        $this->assertTaskTypeNames([$taskTypeName], $this->getRepositoryTaskTypeNames());

        $this->taskTypeMigrator->migrate();

        $this->assertTaskTypeNames($expectedTaskTypeNames, $this->getRepositoryTaskTypeNames());
    }

    /**
     * @return string[]
     */
    private function getRepositoryTaskTypeNames(): array
    {
        $names = [];

        /* @var TaskType[] $taskTypes */
        $taskTypes = $this->repository->findAll();

        foreach ($taskTypes as $state) {
            $this->assertInstanceOf(TaskType::class, $state);
            $names[] = (string)$state;
        }

        sort($names);

        return $names;
    }

    private function assertTaskTypeNames(array $expectedStateNames, array $stateNames)
    {
        sort($expectedStateNames);
        sort($stateNames);

        $this->assertEquals($expectedStateNames, $stateNames);
    }
}
