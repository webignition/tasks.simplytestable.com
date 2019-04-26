<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Entity;

use App\Entity\State;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\Url;
use App\Exception\TaskMutationException;
use App\Tests\Services\ObjectReflector;
use webignition\Uri\Uri;

class TaskTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $jobId = 'x45yHo';
        $urlString = 'http://example.com/';
        $state = new State();
        $type = new TaskType();
        $parameters = 'parameters content';

        $url = Url::create(new Uri($urlString));

        $task = Task::create($jobId, $url, $state, $type, $parameters);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertNull(ObjectReflector::getProperty($task, 'id'));
        $this->assertNull($task->getIdentifier());
        $this->assertSame($jobId, $task->getJobIdentifier());
        $this->assertEquals($urlString, ObjectReflector::getProperty($task, 'url'));
        $this->assertSame($state, $task->getState());
        $this->assertSame($type, ObjectReflector::getProperty($task, 'type'));
        $this->assertNull(ObjectReflector::getProperty($task, 'startDateTime'));
        $this->assertNull(ObjectReflector::getProperty($task, 'endDateTime'));
        $this->assertNull(ObjectReflector::getProperty($task, 'output'));
        $this->assertSame($parameters, ObjectReflector::getProperty($task, 'parameters'));
    }

    public function testSetIdentifier()
    {
        $url = Url::create(new Uri('http://example.com/'));

        $task = Task::create('x45yHo', $url, new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'identifier'));

        $identifier = '7xb467';
        $task->setIdentifier($identifier);
        $this->assertSame($identifier, $task->getIdentifier());
    }

    public function testSetState()
    {
        $url = Url::create(new Uri('http://example.com/'));

        $originalState = new State();
        $newState = new State();

        $task = Task::create('x45yHo', $url, $originalState, new TaskType(), '');
        $this->assertSame($originalState, $task->getState());

        $task->setState($newState);
        $this->assertSame($newState, $task->getState());
    }

    public function testStartStartDateTime()
    {
        $url = Url::create(new Uri('http://example.com/'));

        $startDateTime = new \DateTime();

        $task = Task::create('x45yHo', $url, new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'startDateTime'));

        $task->setStartDateTime($startDateTime);
        $taskStartDateTime = ObjectReflector::getProperty($task, 'startDateTime');
        $this->assertSame($startDateTime, $taskStartDateTime);

        $this->expectException(TaskMutationException::class);
        $this->expectExceptionCode(TaskMutationException::CODE_START_DATE_TIME_ALREADY_SET);

        $task->setStartDateTime($startDateTime);
    }

    public function testSetEndDateTimeNoStartDateTimeSet()
    {
        $url = Url::create(new Uri('http://example.com/'));

        $task = Task::create('x45yHo', $url, new State(), new TaskType(), '');

        $this->expectException(TaskMutationException::class);
        $this->expectExceptionCode(TaskMutationException::CODE_START_DATE_TIME_NOT_SET);

        $task->setEndDateTime(new \DateTime());
    }

    public function testSetEndDateTime()
    {
        $url = Url::create(new Uri('http://example.com/'));

        $startDateTime = new \DateTime();
        $endDateTime = new \DateTime();

        $task = Task::create('x45yHo', $url, new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'endDateTime'));

        $task->setStartDateTime($startDateTime);
        $task->setEndDateTime($endDateTime);

        $taskEndDateTime = ObjectReflector::getProperty($task, 'endDateTime');
        $this->assertSame($endDateTime, $taskEndDateTime);
    }

    /**
     * @dataProvider jsonSerializeDataProvider
     */
    public function testJsonSerialize(callable $taskCreator, array $expectedSerializedData)
    {
        /* @var Task $task */
        $task = $taskCreator();

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($expectedSerializedData, $task->jsonSerialize());
    }

    public function jsonSerializeDataProvider(): array
    {
        $url = Url::create(new Uri('http://example.com/'));

        return [
            'time period not set' => [
                'taskCreator' => function () use ($url) {
                    $task = Task::create(
                        'x45yHo',
                        $url,
                        State::create(Task::STATE_COMPLETED),
                        TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                        'parameters value'
                    );

                    $task->setIdentifier('x123');

                    return $task;
                },
                'expectedSerializedData' => [
                    'id' => 'x123',
                    'job_id' => 'x45yHo',
                    'url' => 'http://example.com/',
                    'type' => TaskType::TYPE_HTML_VALIDATION,
                    'state' => Task::STATE_COMPLETED,
                    'start_date_time' => null,
                    'end_date_time' => null,
                    'parameters' => 'parameters value',
                ],
            ],
            'time period set' => [
                'taskCreator' => function () use ($url) {
                    $task = Task::create(
                        'x45yHo',
                        $url,
                        State::create(Task::STATE_COMPLETED),
                        TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                        'parameters value'
                    );

                    $task->setIdentifier('x123');
                    $task->setStartDateTime(new \DateTime('2019-04-05 19:00'));
                    $task->setEndDateTime(new \DateTime('2019-04-05 19:10'));

                    return $task;
                },
                'expectedSerializedData' => [
                    'id' => 'x123',
                    'job_id' => 'x45yHo',
                    'url' => 'http://example.com/',
                    'type' => TaskType::TYPE_HTML_VALIDATION,
                    'state' => Task::STATE_COMPLETED,
                    'start_date_time' => '2019-04-05T19:00:00+00:00',
                    'end_date_time' => '2019-04-05T19:10:00+00:00',
                    'parameters' => 'parameters value',
                ],
            ],
        ];
    }
}
