<?php

namespace App\Request;

use App\Entity\TaskType;
use Psr\Http\Message\UriInterface;

class CreateTaskRequest
{
    const KEY_JOB_IDENTIFIER = 'job-identifier';
    const KEY_URL = 'url';
    const KEY_TYPE = 'type';
    const KEY_PARAMETERS = 'parameters';

    private $jobIdentifier;
    private $uri;
    private $type;
    private $parameters;

    public function __construct(
        string $jobIdentifier,
        UriInterface $uri,
        ?TaskType $type,
        string $parameters
    ) {
        $this->jobIdentifier = $jobIdentifier;
        $this->uri = $uri;
        $this->type = $type;
        $this->parameters = $parameters;
    }

    public function getJobIdentifier(): string
    {
        return $this->jobIdentifier;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getType(): ?TaskType
    {
        return $this->type;
    }

    public function getParameters(): string
    {
        return $this->parameters;
    }

    public function isValid(): bool
    {
        if (empty($this->jobIdentifier)) {
            return false;
        }

        if ('' === (string) $this->uri) {
            return false;
        }

        if (null === $this->type) {
            return false;
        }

        return true;
    }
}
