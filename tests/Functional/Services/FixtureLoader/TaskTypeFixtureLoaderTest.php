<?php

namespace App\Tests\Functional\Services\FixtureLoader;

use App\Entity\TaskType;
use App\Services\FixtureLoader\TaskTypeFixtureLoader;

class TaskTypeFixtureLoaderTest extends AbstractFixtureLoaderTest
{
    /**
     * @var TaskTypeFixtureLoader
     */
    private $taskTypeFixtureLoader;

    /**
     * @var string[]
     */
    private $taskTypeNames;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskTypeFixtureLoader = self::$container->get(TaskTypeFixtureLoader::class);
        $taskTypeNames = self::$container->get('app.services.data-provider.task-types');
        $this->taskTypeNames = $taskTypeNames->getData();
    }

    protected function getEntityClass(): string
    {
        return TaskType::class;
    }

    protected function getEntityClassesToRemove(): array
    {
        return [
            TaskType::class,
        ];
    }

    public function testLoadFromEmpty()
    {
        $this->assertEmpty($this->repository->findAll());

        $this->taskTypeFixtureLoader->load();

        $this->assertTaskTypeNames($this->taskTypeNames, $this->getRepositoryTaskTypeNames());
    }

    public function testLoadFromNonEmpty()
    {
        $expectedTaskTypeNames = $this->taskTypeNames;
        sort($expectedTaskTypeNames);

        $this->assertEmpty($this->repository->findAll());

        $taskTypeName = $expectedTaskTypeNames[0];

        $taskType = TaskType::create($taskTypeName);
        $this->entityManager->persist($taskType);
        $this->entityManager->flush();

        $this->assertTaskTypeNames([$taskTypeName], $this->getRepositoryTaskTypeNames());

        $this->taskTypeFixtureLoader->load();

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

        foreach ($taskTypes as $taskType) {
            $this->assertInstanceOf(TaskType::class, $taskType);
            $names[] = (string)$taskType;
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
