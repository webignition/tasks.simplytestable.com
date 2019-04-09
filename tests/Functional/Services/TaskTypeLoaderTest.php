<?php

namespace App\Tests\Functional\Services;

use App\Entity\TaskType;
use App\Services\TaskTypeLoader;
use App\Tests\Functional\AbstractBaseTestCase;

class TaskTypeLoaderTest extends AbstractBaseTestCase
{
    /**
     * @var TaskTypeLoader
     */
    private $taskTypeLoader;

    /**
     * @var string[]
     */
    private $taskTypeNames;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskTypeLoader = self::$container->get(TaskTypeLoader::class);

        $taskTypeNames = self::$container->get('app.services.task_type_names');
        $this->taskTypeNames = $taskTypeNames->getData();
    }

    public function testLoadKnown()
    {
        foreach ($this->taskTypeNames as $taskTypeName) {
            $state = $this->taskTypeLoader->load($taskTypeName);

            $this->assertInstanceOf(TaskType::class, $state);
        }
    }

    public function testLoadUnknown()
    {
        $this->assertNull($this->taskTypeLoader->load('foo'));
    }
}
