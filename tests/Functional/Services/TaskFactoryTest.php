<?php

namespace App\Tests\Functional\Services;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\Url;
use App\Services\StateLoader;
use App\Services\TaskFactory;
use App\Services\TaskTypeLoader;
use App\Tests\Functional\AbstractBaseTestCase;
use App\Tests\Services\ObjectReflector;

class TaskFactoryTest extends AbstractBaseTestCase
{
    /**
     * @var TaskFactory
     */
    private $taskFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskFactory = self::$container->get(TaskFactory::class);
    }

    public function testCreate()
    {
        $stateLoader = self::$container->get(StateLoader::class);
        $taskTypeLoader = self::$container->get(TaskTypeLoader::class);

        $jobId = 'x45yHo';
        $urlString = 'http://example.com/';
        $state = $stateLoader->load('task-' . Task::STATE_NEW);

        $parameters = 'parameters content';
        $type = $taskTypeLoader->load(TaskType::TYPE_HTML_VALIDATION);

        if ($type instanceof TaskType) {
            $task = $this->taskFactory->create($jobId, $urlString, $type, $parameters);

            $this->assertInstanceOf(Task::class, $task);
            $this->assertNotNull($task->getId());
            $this->assertNotNull($task->getIdentifier());
            $this->assertSame($jobId, $task->getJobIdentifier());
            $this->assertSame($state, $task->getState());
            $this->assertSame($type, ObjectReflector::getProperty($task, 'type'));
            $this->assertNull(ObjectReflector::getProperty($task, 'timePeriod'));
            $this->assertNull(ObjectReflector::getProperty($task, 'output'));
            $this->assertSame($parameters, ObjectReflector::getProperty($task, 'parameters'));

            $url = ObjectReflector::getProperty($task, 'url');

            $this->assertInstanceOf(Url::class, $url);
            $this->assertEquals($urlString, $url);
        }
    }
}
