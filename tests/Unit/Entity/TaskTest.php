<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Unit\Entity;

use App\Entity\Output;
use App\Entity\State;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Exception\TaskMutationException;
use App\Tests\Services\ObjectReflector;
use webignition\InternetMediaType\InternetMediaType;

class TaskTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $jobId = 'x45yHo';
        $url = 'http://example.com/';
        $state = new State();
        $type = new TaskType();
        $parameters = 'parameters content';

        $task = Task::create($jobId, $url, $state, $type, $parameters);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertNull(ObjectReflector::getProperty($task, 'id'));
        $this->assertNull($task->getIdentifier());
        $this->assertSame($jobId, $task->getJobIdentifier());
        $this->assertSame($url, ObjectReflector::getProperty($task, 'url'));
        $this->assertSame($state, $task->getState());
        $this->assertSame($type, ObjectReflector::getProperty($task, 'type'));
        $this->assertNull(ObjectReflector::getProperty($task, 'timePeriod'));
        $this->assertNull(ObjectReflector::getProperty($task, 'output'));
        $this->assertSame($parameters, ObjectReflector::getProperty($task, 'parameters'));
    }

    public function testSetIdentifier()
    {
        $task = Task::create('x45yHo', 'http://example.com/', new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'identifier'));

        $identifier = '7xb467';
        $task->setIdentifier($identifier);
        $this->assertSame($identifier, $task->getIdentifier());
    }

    public function testSetState()
    {
        $originalState = new State();
        $newState = new State();

        $task = Task::create('x45yHo', 'http://example.com/', $originalState, new TaskType(), '');
        $this->assertSame($originalState, $task->getState());

        $task->setState($newState);
        $this->assertSame($newState, $task->getState());
    }

    public function testStartStartDateTime()
    {
        $startDateTime = new \DateTime();

        $task = Task::create('x45yHo', 'http://example.com/', new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'timePeriod'));

        $task->setStartDateTime($startDateTime);
        $timePeriod = ObjectReflector::getProperty($task, 'timePeriod');
        $this->assertNotNull($timePeriod);

        $taskStartDateTime = ObjectReflector::getProperty($timePeriod, 'startDateTime');
        $this->assertSame($startDateTime, $taskStartDateTime);

        $this->expectException(TaskMutationException::class);
        $this->expectExceptionCode(TaskMutationException::CODE_START_DATE_TIME_ALREADY_SET);

        $task->setStartDateTime($startDateTime);
    }

    public function testSetEndDateTimeNoStartDateTimeSet()
    {
        $task = Task::create('x45yHo', 'http://example.com/', new State(), new TaskType(), '');

        $this->expectException(TaskMutationException::class);
        $this->expectExceptionCode(TaskMutationException::CODE_START_DATE_TIME_NOT_SET);

        $task->setEndDateTime(new \DateTime());
    }

    public function testSetEndDateTime()
    {
        $startDateTime = new \DateTime();
        $endDateTime = new \DateTime();

        $task = Task::create('x45yHo', 'http://example.com/', new State(), new TaskType(), '');
        $this->assertNull(ObjectReflector::getProperty($task, 'timePeriod'));

        $task->setStartDateTime($startDateTime);
        $task->setEndDateTime($endDateTime);

        $timePeriod = ObjectReflector::getProperty($task, 'timePeriod');
        $this->assertNotNull($timePeriod);

        $taskEndDateTime = ObjectReflector::getProperty($timePeriod, 'endDateTime');
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
        return [
            'time period not set, output not set' => [
                'taskCreator' => function () {
                    $task = Task::create(
                        'x45yHo',
                        'http://example.com/',
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
                    'time_period' => [],
                    'parameters' => 'parameters value',
                    'output' => null,
                ],
            ],
            'time period set, output set' => [
                'taskCreator' => function () {
                    $task = Task::create(
                        'x45yHo',
                        'http://example.com/',
                        State::create(Task::STATE_COMPLETED),
                        TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                        'parameters value'
                    );

                    $task->setIdentifier('x123');
                    $task->setStartDateTime(new \DateTime('2019-04-05 19:00'));
                    $task->setEndDateTime(new \DateTime('2019-04-05 19:10'));

                    $task->setOutput(Output::create(
                        'output content',
                        new InternetMediaType('text', 'plain'),
                        1,
                        2
                    ));

                    return $task;
                },
                'expectedSerializedData' => [
                    'id' => 'x123',
                    'job_id' => 'x45yHo',
                    'url' => 'http://example.com/',
                    'type' => TaskType::TYPE_HTML_VALIDATION,
                    'state' => Task::STATE_COMPLETED,
                    'time_period' => [
                        'start_date_time' => '2019-04-05T19:00:00+00:00',
                        'end_date_time' => '2019-04-05T19:10:00+00:00',
                    ],
                    'parameters' => 'parameters value',
                    'output' => [
                        'content' => 'output content',
                        'content_type' => 'text/plain',
                        'error_count' => 1,
                        'warning_count' => 2,
                    ],
                ],
            ],
        ];
    }
}
