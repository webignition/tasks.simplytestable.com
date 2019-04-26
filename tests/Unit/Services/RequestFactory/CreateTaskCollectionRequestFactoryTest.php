<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\Services\RequestFactory;

use App\Entity\TaskType;
use App\Request\CreateTaskCollectionRequest;
use App\Request\CreateTaskRequest;
use App\Services\RequestFactory\CreateTaskCollectionRequestFactory;
use App\Services\RequestFactory\CreateTaskRequestFactory;
use App\Services\TaskTypeLoader;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Request;
use webignition\CreateTaskCollectionPayload\Factory as PayloadFactory;
use webignition\CreateTaskCollectionPayload\Payload;
use webignition\CreateTaskCollectionPayload\TaskPayload;
use webignition\CreateTaskCollectionPayload\TypeInterface;
use webignition\Uri\Uri;

class CreateTaskCollectionRequestFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createInvalidRequestDataProvider
     */
    public function testCreateInvalidRequest(
        Request $request
    ) {
        $factory = new CreateTaskCollectionRequestFactory(
            new PayloadFactory(),
            \Mockery::mock(CreateTaskRequestFactory::class)
        );

        $createTaskCollectionRequest = $factory->create($request);

        $this->assertEquals(new CreateTaskCollectionRequest('', []), $createTaskCollectionRequest);
        $this->assertFalse($createTaskCollectionRequest->isValid());
    }

    public function createInvalidRequestDataProvider(): array
    {
        return [
            'invalid content type produces empty request' => [
                'request' => new Request(),
            ],
            'request body is not json' => [
                'request' => $this->createJsonRequestFromString('foo'),
            ],
            'request body is not json array' => [
                'request' => $this->createJsonRequestFromString('"foo"'),
            ],
            'missing job identifier' => [
                'request' => $this->createJsonRequestFromArray([]),
            ],
            'empty job identifier' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => '',
                ]),
            ],
            'missing tasks' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                ]),
            ],
            'empty tasks' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                    Payload::KEY_TASKS => [],
                ]),
            ],
        ];
    }

    /**
     * @dataProvider createSuccessDataProvider
     */
    public function testCreateSuccess(
        Request $request,
        CreateTaskRequestFactory $createTaskRequestFactory,
        CreateTaskCollectionRequest $expectedCreateTaskCollectionRequest,
        bool $expectedIsValid
    ) {
        $factory = new CreateTaskCollectionRequestFactory(new PayloadFactory(), $createTaskRequestFactory);

        $createTaskCollectionRequest = $factory->create($request);

        $this->assertEquals($expectedCreateTaskCollectionRequest, $createTaskCollectionRequest);
        $this->assertEquals($expectedIsValid, $createTaskCollectionRequest->isValid());
    }

    public function createSuccessDataProvider(): array
    {
        $htmlValidationTaskType = TaskType::create(TaskType::TYPE_HTML_VALIDATION);
        $cssValidationTaskType = TaskType::create(TaskType::TYPE_CSS_VALIDATION);
        $linkIntegrityTaskType = TaskType::create(TaskType::TYPE_LINK_INTEGRITY);

        return [
            'request with single invalid is invalid' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                    Payload::KEY_TASKS => [
                        [
                            TaskPayload::KEY_TYPE => TypeInterface::HTML_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                    ],
                ]),
                'createTaskRequestFactory' => new CreateTaskRequestFactory(
                    $this->createTaskTypeLoader([
                        TypeInterface::HTML_VALIDATION => true,
                    ])
                ),
                'expectedCreateTaskCollectionRequest' => new CreateTaskCollectionRequest('', []),
                'expectedIsValid' => false,
            ],
            'request with single valid task is valid' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                    Payload::KEY_TASKS => [
                        [
                            TaskPayload::KEY_URI => 'http://example.com/',
                            TaskPayload::KEY_TYPE => TypeInterface::HTML_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                    ],
                ]),
                'createTaskRequestFactory' => new CreateTaskRequestFactory(
                    $this->createTaskTypeLoader([
                        TypeInterface::HTML_VALIDATION => true,
                    ])
                ),
                'expectedCreateTaskCollectionRequest' => new CreateTaskCollectionRequest(
                    'x123',
                    [
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/'),
                            $htmlValidationTaskType,
                            '[]'
                        ),
                    ]
                ),
                'expectedIsValid' => true,
            ],
            'request with multiple valid tasks is valid' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                    Payload::KEY_TASKS => [
                        [
                            TaskPayload::KEY_URI => 'http://example.com/html',
                            TaskPayload::KEY_TYPE => TypeInterface::HTML_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                        [
                            TaskPayload::KEY_URI => 'http://example.com/css',
                            TaskPayload::KEY_TYPE => TypeInterface::CSS_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                        [
                            TaskPayload::KEY_URI => 'http://example.com/link-integrity',
                            TaskPayload::KEY_TYPE => TypeInterface::LINK_INTEGRITY,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                    ],
                ]),
                'createTaskRequestFactory' => new CreateTaskRequestFactory(
                    $this->createTaskTypeLoader([
                        TypeInterface::HTML_VALIDATION => true,
                        TypeInterface::CSS_VALIDATION => true,
                        TypeInterface::LINK_INTEGRITY => true,
                    ])
                ),
                'expectedCreateTaskCollectionRequest' => new CreateTaskCollectionRequest(
                    'x123',
                    [
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/html'),
                            $htmlValidationTaskType,
                            '[]'
                        ),
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/css'),
                            $cssValidationTaskType,
                            '[]'
                        ),
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/link-integrity'),
                            $linkIntegrityTaskType,
                            '[]'
                        ),
                    ]
                ),
                'expectedIsValid' => true,
            ],
            'invalid task is ignored' => [
                'request' => $this->createJsonRequestFromArray([
                    Payload::KEY_JOB_IDENTIFIER => 'x123',
                    Payload::KEY_TASKS => [
                        [
                            TaskPayload::KEY_URI => 'http://example.com/html',
                            TaskPayload::KEY_TYPE => TypeInterface::HTML_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                        [
                            TaskPayload::KEY_URI => 'http://example.com/css',
                            TaskPayload::KEY_TYPE => TypeInterface::CSS_VALIDATION,
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                        [
                            TaskPayload::KEY_URI => 'http://example.com/link-integrity',
                            TaskPayload::KEY_PARAMETERS => '',
                        ],
                    ],
                ]),
                'createTaskRequestFactory' => new CreateTaskRequestFactory(
                    $this->createTaskTypeLoader([
                        TypeInterface::HTML_VALIDATION => true,
                        TypeInterface::CSS_VALIDATION => true,
                    ])
                ),
                'expectedCreateTaskCollectionRequest' => new CreateTaskCollectionRequest(
                    'x123',
                    [
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/html'),
                            $htmlValidationTaskType,
                            '[]'
                        ),
                        new CreateTaskRequest(
                            'x123',
                            new Uri('http://example.com/css'),
                            $cssValidationTaskType,
                            '[]'
                        ),
                    ]
                ),
                'expectedIsValid' => true,
            ],
        ];
    }

    private function createJsonRequestFromArray(array $data)
    {
        $request = new Request([], [], [], [], [], [], (string) json_encode($data));
        $request->headers->set('content-type', 'application/json');

        return $request;
    }

    private function createJsonRequestFromString(string $content)
    {
        $request = new Request([], [], [], [], [], [], $content);
        $request->headers->set('content-type', 'application/json');

        return $request;
    }

    /**
     * @return MockInterface|TaskTypeLoader
     */
    private function createTaskTypeLoader(array $loadCalls): MockInterface
    {
        $taskTypeLoader = \Mockery::mock(TaskTypeLoader::class);

        foreach ($loadCalls as $taskTypeString => $exists) {
            if ($exists) {
                $taskTypeLoader
                    ->shouldReceive('load')
                    ->with($taskTypeString)
                    ->andReturn(TaskType::create($taskTypeString));
            } else {
                $taskTypeLoader
                    ->shouldReceive('load')
                    ->with($taskTypeString)
                    ->andReturn();
            }
        }

        return $taskTypeLoader;
    }
}
