<?php

namespace App\Services\RequestFactory;

use App\Request\CreateTaskCollectionRequest;
use Symfony\Component\HttpFoundation\Request;
use webignition\CreateTaskCollectionPayload\Factory as PayloadFactory;
use webignition\CreateTaskCollectionPayload\InvalidPayloadDataException;

class CreateTaskCollectionRequestFactory
{
    private $payloadFactory;
    private $createTaskRequestFactory;

    public function __construct(PayloadFactory $payloadFactory, CreateTaskRequestFactory $createTaskRequestFactory)
    {
        $this->payloadFactory = $payloadFactory;
        $this->createTaskRequestFactory = $createTaskRequestFactory;
    }

    public function create(Request $request)
    {
        $emptyRequest = new CreateTaskCollectionRequest('', []);

        if ('json' !== $request->getContentType()) {
            return $emptyRequest;
        }

        $requestData = json_decode($request->getContent(), true);

        if (!is_array($requestData)) {
            return $emptyRequest;
        }

        try {
            $payload = $this->payloadFactory->createFromArray($requestData);

            $jobIdentifier = $payload->getJobIdentifier();
            $createTaskRequests = [];

            foreach ($payload->getTaskPayloads() as $taskPayload) {
                $createTaskRequests[] = $this->createTaskRequestFactory->createFromTaskPayload(
                    $taskPayload,
                    $jobIdentifier
                );
            }

            return new CreateTaskCollectionRequest($jobIdentifier, $createTaskRequests);
        } catch (InvalidPayloadDataException $invalidPayloadDataException) {
            return $emptyRequest;
        }
    }
}
