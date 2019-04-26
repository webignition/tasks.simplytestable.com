<?php

namespace App\Services\RequestFactory;

use App\Request\CreateTaskRequest;
use App\Services\TaskTypeLoader;
use Psr\Http\Message\UriInterface;
use Symfony\Component\HttpFoundation\Request;
use webignition\CreateTaskCollectionPayload\TaskPayload;
use webignition\Uri\Normalizer;
use webignition\Uri\Uri;

class CreateTaskRequestFactory
{
    private $taskTypeLoader;

    public function __construct(TaskTypeLoader $taskTypeLoader)
    {
        $this->taskTypeLoader = $taskTypeLoader;
    }

    public function createFromRequest(Request $request)
    {
        $requestData = $request->request;

        return $this->create(
            $requestData->get(CreateTaskRequest::KEY_JOB_IDENTIFIER, ''),
            new Uri(trim($requestData->get(CreateTaskRequest::KEY_URL, ''))),
            $requestData->get(CreateTaskRequest::KEY_TYPE, ''),
            $requestData->get(CreateTaskRequest::KEY_PARAMETERS, '')
        );
    }

    public function createFromTaskPayload(TaskPayload $taskPayload, string $jobIdentifier)
    {
        return $this->create(
            $jobIdentifier,
            $taskPayload->getUri(),
            $taskPayload->getType(),
            $taskPayload->getParameters()
        );
    }

    private function create(string $jobIdentifier, UriInterface $uri, string $type, string $parameters)
    {
        $jobIdentifier = trim($jobIdentifier);
        $type = trim($type);

        $uri = Normalizer::normalize($uri);
        $taskType = $this->taskTypeLoader->load($type);

        $parametersArray = json_decode($parameters, true);
        if (!is_array($parametersArray) || is_array($parametersArray) && empty($parametersArray)) {
            $parameters = (string) json_encode([]);
        }

        return new CreateTaskRequest($jobIdentifier, $uri, $taskType, $parameters);
    }
}
