<?php

namespace App\Services\RequestFactory;

use App\Request\CreateTaskRequest;
use App\Services\TaskTypeLoader;
use Symfony\Component\HttpFoundation\Request;
use webignition\Uri\Normalizer;
use webignition\Uri\Uri;

class CreateTaskRequestFactory
{
    private $taskTypeLoader;

    public function __construct(TaskTypeLoader $taskTypeLoader)
    {
        $this->taskTypeLoader = $taskTypeLoader;
    }

    public function create(Request $request)
    {
        $requestData = $request->request;

        $jobIdentifier = $requestData->get(CreateTaskRequest::KEY_JOB_IDENTIFIER);
        $url = $requestData->get(CreateTaskRequest::KEY_URL);
        $type = $requestData->get(CreateTaskRequest::KEY_TYPE);
        $parameters = $requestData->get(CreateTaskRequest::KEY_PARAMETERS);

        $uri = new Uri($url);
        $uri = Normalizer::normalize($uri);
        $taskType = $this->taskTypeLoader->load($type);

        $parametersArray = json_decode($parameters, true);
        if (!is_array($parametersArray) || is_array($parametersArray) && empty($parametersArray)) {
            $parameters = json_encode([]);
        }

        return new CreateTaskRequest($jobIdentifier, $uri, $taskType, $parameters);
    }
}
