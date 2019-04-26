<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\Services\RequestFactory;

use App\Entity\TaskType;
use App\Request\CreateTaskRequest;
use App\Services\RequestFactory\CreateTaskRequestFactory;
use App\Services\TaskTypeLoader;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use webignition\CreateTaskCollectionPayload\TaskPayload;
use webignition\Uri\Uri;

class CreateTaskRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromRequestDataProvider
     */
    public function testCreateFromRequest(
        Request $request,
        TaskTypeLoader $taskTypeLoader,
        CreateTaskRequest $expectedCreateTaskRequest
    ) {
        $factory = new CreateTaskRequestFactory($taskTypeLoader);

        $createTaskRequest = $factory->createFromRequest($request);

        $this->assertEquals($expectedCreateTaskRequest, $createTaskRequest);
    }

    public function createFromRequestDataProvider(): array
    {
        $htmlValidationTaskType = TaskType::create(TaskType::TYPE_HTML_VALIDATION);

        return [
            'create task request is created' => [
                'request' => new Request([], [
                    CreateTaskRequest::KEY_JOB_IDENTIFIER => 'x123',
                    CreateTaskRequest::KEY_URL => 'http://example.com/',
                    CreateTaskRequest::KEY_TYPE => TaskType::TYPE_HTML_VALIDATION,
                    CreateTaskRequest::KEY_PARAMETERS => '[]',
                ]),
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'uri is normalised' => [
                'request' => new Request([], [
                    CreateTaskRequest::KEY_JOB_IDENTIFIER => 'x123',
                    CreateTaskRequest::KEY_URL => 'http://example.com/../../',
                    CreateTaskRequest::KEY_TYPE => TaskType::TYPE_HTML_VALIDATION,
                    CreateTaskRequest::KEY_PARAMETERS => '[]',
                ]),
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'invalid task type' => [
                'request' => new Request([], [
                    CreateTaskRequest::KEY_JOB_IDENTIFIER => 'x123',
                    CreateTaskRequest::KEY_URL => 'http://example.com/../../',
                    CreateTaskRequest::KEY_TYPE => 'foo',
                    CreateTaskRequest::KEY_PARAMETERS => '[]',
                ]),
                'taskTypeLoader' => $this->createTaskTypeLoader('foo', null),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    null,
                    '[]'
                ),
            ],
            'parameters are normalised (empty string to json array)' => [
                'request' => new Request([], [
                    CreateTaskRequest::KEY_JOB_IDENTIFIER => 'x123',
                    CreateTaskRequest::KEY_URL => 'http://example.com/',
                    CreateTaskRequest::KEY_TYPE => TaskType::TYPE_HTML_VALIDATION,
                    CreateTaskRequest::KEY_PARAMETERS => '',
                ]),
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'parameters are normalised (json object to json array)' => [
                'request' => new Request([], [
                    CreateTaskRequest::KEY_JOB_IDENTIFIER => 'x123',
                    CreateTaskRequest::KEY_URL => 'http://example.com/',
                    CreateTaskRequest::KEY_TYPE => TaskType::TYPE_HTML_VALIDATION,
                    CreateTaskRequest::KEY_PARAMETERS => '{}',
                ]),
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
        ];
    }

    /**
     * @dataProvider createFromTaskPayloadDataProvider
     */
    public function testCreateFromTaskPayload(
        TaskPayload $taskPayload,
        string $jobIdentifier,
        TaskTypeLoader $taskTypeLoader,
        CreateTaskRequest $expectedCreateTaskRequest
    ) {
        $factory = new CreateTaskRequestFactory($taskTypeLoader);

        $createTaskRequest = $factory->createFromTaskPayload($taskPayload, $jobIdentifier);

        $this->assertEquals($expectedCreateTaskRequest, $createTaskRequest);
    }

    public function createFromTaskPayloadDataProvider(): array
    {
        $htmlValidationTaskType = TaskType::create(TaskType::TYPE_HTML_VALIDATION);

        return [
            'create task request is created' => [
                'taskPayload' => new TaskPayload(
                    new Uri('http://example.com/'),
                    TaskType::TYPE_HTML_VALIDATION,
                    '[]'
                ),
                'jobIdentifier' => 'x123',
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'uri is normalised' => [
                'taskPayload' => new TaskPayload(
                    new Uri('http://example.com/../../'),
                    TaskType::TYPE_HTML_VALIDATION,
                    '[]'
                ),
                'jobIdentifier' => 'x123',
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'invalid task type' => [
                'taskPayload' => new TaskPayload(
                    new Uri('http://example.com/../../'),
                    'foo',
                    '[]'
                ),
                'jobIdentifier' => 'x123',
                'taskTypeLoader' => $this->createTaskTypeLoader('foo', null),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    null,
                    '[]'
                ),
            ],
            'parameters are normalised (empty string to json array)' => [
                'taskPayload' => new TaskPayload(
                    new Uri('http://example.com/'),
                    TaskType::TYPE_HTML_VALIDATION,
                    ''
                ),
                'jobIdentifier' => 'x123',
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
            'parameters are normalised (json object to json array)' => [
                'taskPayload' => new TaskPayload(
                    new Uri('http://example.com/'),
                    TaskType::TYPE_HTML_VALIDATION,
                    '{}'
                ),
                'jobIdentifier' => 'x123',
                'taskTypeLoader' => $this->createTaskTypeLoader(
                    TaskType::TYPE_HTML_VALIDATION,
                    $htmlValidationTaskType
                ),
                'expectedCreateTaskRequest' => new CreateTaskRequest(
                    'x123',
                    new Uri('http://example.com/'),
                    $htmlValidationTaskType,
                    '[]'
                ),
            ],
        ];
    }

    /**
     * @return MockInterface|TaskTypeLoader
     */
    private function createTaskTypeLoader(string $taskTypeString, ?TaskType $taskType): MockInterface
    {
        $taskTypeLoader = \Mockery::mock(TaskTypeLoader::class);

        $taskTypeLoader
            ->shouldReceive('load')
            ->with($taskTypeString)
            ->andReturn($taskType);

        return $taskTypeLoader;
    }
}
