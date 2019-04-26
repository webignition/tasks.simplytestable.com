<?php

namespace App\Tests\Functional\Controller;

use App\Controller\TasksController;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Request\CreateTaskCollectionRequest;
use App\Services\TaskFactory;
use App\Tests\Services\ObjectReflector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @group Controller/TasksController
 */
class TasksControllerTest extends AbstractControllerTest
{
    public function testCreateActionGetRequest()
    {
        $requestUrl = $this->router->generate('tasks_create');

        $this->expectException(MethodNotAllowedHttpException::class);

        $this->client->request(
            'GET',
            $requestUrl
        );
    }

    public function testCreateActionPostRequest()
    {
        $requestUrl = $this->router->generate('tasks_create');

        $content = (string) json_encode([
            'job-identifier' => 'x123',
            'tasks' => [
                [
                    'uri' => 'http://example.com',
                    'type' => TaskType::TYPE_HTML_VALIDATION,
                ],
            ],
        ]);

        $this->client->request(
            'POST',
            $requestUrl,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );

        $response = $this->client->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertIsArray($responseData);
        $this->assertCount(1, $responseData);

        $taskData = $responseData[0];

        $this->assertIsArray($taskData);

        $this->assertArrayHasKey('id', $taskData);
        $this->assertArrayHasKey('job_id', $taskData);
        $this->assertArrayHasKey('url', $taskData);
        $this->assertArrayHasKey('type', $taskData);
        $this->assertArrayHasKey('state', $taskData);
        $this->assertArrayHasKey('start_date_time', $taskData);
        $this->assertArrayHasKey('end_date_time', $taskData);
        $this->assertArrayHasKey('parameters', $taskData);
        $this->assertArrayHasKey('output', $taskData);

        $identifier = $taskData['id'];
        $this->assertIsString($identifier);
        $this->assertNotEmpty($identifier);

        $entityManager = self::$container->get(EntityManagerInterface::class);
        $taskRepository = $entityManager->getRepository(Task::class);

        /* @var Task $task */
        $task = $taskRepository->findOneBy([
            'identifier' => $identifier,
        ]);

        $this->assertInstanceOf(Task::class, $task);

        if ($task instanceof Task) {
            $this->assertNotNull($task->getId());
            $this->assertSame($taskData['id'], $task->getIdentifier());
            $this->assertSame($taskData['job_id'], $task->getJobIdentifier());
            $this->assertSame($taskData['url'], ObjectReflector::getProperty($task, 'url'));
            $this->assertEquals($taskData['type'], ObjectReflector::getProperty($task, 'type'));
            $this->assertEquals($taskData['state'], $task->getState());
            $this->assertNull($taskData['start_date_time']);
            $this->assertNull(ObjectReflector::getProperty($task, 'startDateTime'));
            $this->assertNull($taskData['end_date_time']);
            $this->assertNull(ObjectReflector::getProperty($task, 'endDateTime'));
            $this->assertSame($taskData['parameters'], ObjectReflector::getProperty($task, 'parameters'));
            $this->assertNull($taskData['output']);
            $this->assertNull(ObjectReflector::getProperty($task, 'output'));
        }
    }

    public function testCreateActionBadRequest()
    {
        $tasksController = self::$container->get(TasksController::class);
        $taskFactory = self::$container->get(TaskFactory::class);

        $this->expectException(BadRequestHttpException::class);

        $createTaskCollectionRequest = \Mockery::mock(CreateTaskCollectionRequest::class);
        $createTaskCollectionRequest
            ->shouldReceive('isValid')
            ->andReturnFalse();

        $tasksController->createAction($createTaskCollectionRequest, $taskFactory);
    }
}
