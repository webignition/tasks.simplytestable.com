<?php

namespace App\ArgumentResolver;

use App\Request\CreateTaskRequest;
use App\Services\RequestFactory\CreateTaskRequestFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateTaskRequestValueResolver implements ArgumentValueResolverInterface
{
    private $factory;

    public function __construct(CreateTaskRequestFactory $factory)
    {
        $this->factory = $factory;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return CreateTaskRequest::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->factory->create($request);
    }
}
