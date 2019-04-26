<?php

namespace App\ArgumentResolver;

use App\Request\CreateTaskCollectionRequest;
use App\Services\RequestFactory\CreateTaskCollectionRequestFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateTaskCollectionRequestValueResolver implements ArgumentValueResolverInterface
{
    private $factory;

    public function __construct(CreateTaskCollectionRequestFactory $factory)
    {
        $this->factory = $factory;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return CreateTaskCollectionRequest::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->factory->create($request);
    }
}
