<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\Request;

use App\Entity\TaskType;
use App\Request\CreateTaskRequest;
use Psr\Http\Message\UriInterface;
use webignition\Uri\Uri;

class CreateTaskRequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        string $jobIdentifier,
        UriInterface $uri,
        ?TaskType $type,
        string $parameters,
        bool $expectedIsValid
    ) {
        $createTaskRequest = new CreateTaskRequest($jobIdentifier, $uri, $type, $parameters);

        $this->assertSame($jobIdentifier, $createTaskRequest->getJobIdentifier());
        $this->assertSame($uri, $createTaskRequest->getUri());
        $this->assertSame($type, $createTaskRequest->getType());
        $this->assertSame($parameters, $createTaskRequest->getParameters());
        $this->assertSame($expectedIsValid, $createTaskRequest->isValid());
    }

    public function createDataProvider(): array
    {
        return [
            'empty job identifier is not valid' => [
                'jobIdentifier' => '',
                'uri' => new Uri('http://example.com/'),
                'type' => TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                'parameters' => '',
                'expectedIsValid' => false,
            ],
            'empty uri is not valid' => [
                'jobIdentifier' => 'x123',
                'uri' => new Uri(''),
                'type' => TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                'parameters' => '',
                'expectedIsValid' => false,
            ],
            'null task type is not valid' => [
                'jobIdentifier' => 'x123',
                'uri' => new Uri('http://example.com/'),
                'type' => null,
                'parameters' => '',
                'expectedIsValid' => false,
            ],
            'valid, no parameters' => [
                'jobIdentifier' => 'x123',
                'uri' => new Uri('http://example.com/'),
                'type' => TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                'parameters' => '',
                'expectedIsValid' => true,
            ],
            'valid, has parameters' => [
                'jobIdentifier' => 'x123',
                'uri' => new Uri('http://example.com/'),
                'type' => TaskType::create(TaskType::TYPE_HTML_VALIDATION),
                'parameters' => '[]',
                'expectedIsValid' => true,
            ],
        ];
    }
}
