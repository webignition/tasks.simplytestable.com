<?php

namespace App\Tests\Functional\Controller;

use App\Controller\TaskController;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Request\CreateTaskRequest;
use App\Services\TaskFactory;
use App\Tests\Services\ObjectReflector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * @group Controller/TaskController
 */
class TaskControllerTest extends AbstractControllerTest
{
    public function testCreateActionGetRequest()
    {
        $requestUrl = $this->router->generate('task_create');

        $this->expectException(MethodNotAllowedHttpException::class);

        $this->client->request(
            'GET',
            $requestUrl
        );
    }

    public function testCreateActionPostRequest()
    {
        $requestUrl = $this->router->generate('task_create');

        $this->client->request(
            'POST',
            $requestUrl,
            [
                'job-identifier' => 'x123',
                'url' => 'http://example.com/',
                'type' => TaskType::TYPE_HTML_VALIDATION,
                'parameters' => '{}',
            ]
        );

        $response = $this->client->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('job_id', $responseData);
        $this->assertArrayHasKey('url', $responseData);
        $this->assertArrayHasKey('type', $responseData);
        $this->assertArrayHasKey('state', $responseData);
        $this->assertArrayHasKey('start_date_time', $responseData);
        $this->assertArrayHasKey('end_date_time', $responseData);
        $this->assertArrayHasKey('parameters', $responseData);
        $this->assertArrayHasKey('output', $responseData);

        $identifier = $responseData['id'];
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
            $this->assertSame($responseData['id'], $task->getIdentifier());
            $this->assertSame($responseData['job_id'], $task->getJobIdentifier());
            $this->assertSame($responseData['url'], ObjectReflector::getProperty($task, 'url'));
            $this->assertEquals($responseData['type'], ObjectReflector::getProperty($task, 'type'));
            $this->assertEquals($responseData['state'], $task->getState());
            $this->assertNull($responseData['start_date_time']);
            $this->assertNull(ObjectReflector::getProperty($task, 'startDateTime'));
            $this->assertNull($responseData['end_date_time']);
            $this->assertNull(ObjectReflector::getProperty($task, 'endDateTime'));
            $this->assertSame($responseData['parameters'], ObjectReflector::getProperty($task, 'parameters'));
            $this->assertNull($responseData['output']);
            $this->assertNull(ObjectReflector::getProperty($task, 'output'));
        }
    }

    public function testCreateActionBadRequest()
    {
        $taskController = self::$container->get(TaskController::class);
        $taskFactory = self::$container->get(TaskFactory::class);

        $this->expectException(BadRequestHttpException::class);

        $createTaskRequest = \Mockery::mock(CreateTaskRequest::class);
        $createTaskRequest
            ->shouldReceive('isValid')
            ->andReturnFalse();

        $createTaskRequest
            ->shouldReceive('getType')
            ->andReturnNull();

        $taskController->createAction($createTaskRequest, $taskFactory);
    }
}
