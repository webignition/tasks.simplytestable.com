<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\ArgumentResolver;

use App\ArgumentResolver\CreateTaskRequestValueResolver;
use App\Request\CreateTaskRequest;
use App\Services\RequestFactory\CreateTaskRequestFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateTaskRequestValueResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(string $argumentType, bool $expectedSupports)
    {
        $valueResolver = new CreateTaskRequestValueResolver(
            \Mockery::mock(CreateTaskRequestFactory::class)
        );

        $argumentMetaData = \Mockery::mock(ArgumentMetadata::class);
        $argumentMetaData
            ->shouldReceive('getType')
            ->andReturn($argumentType);

        $supports = $valueResolver->supports(new Request(), $argumentMetaData);

        $this->assertEquals($expectedSupports, $supports);
    }

    public function supportsDataProvider(): array
    {
        return [
            'does not support' => [
                'argumentType' => 'foo',
                'expectedSupports' => false,
            ],
            'does support' => [
                'argumentType' => CreateTaskRequest::class,
                'expectedSupports' => true,
            ],
        ];
    }

    public function testResolve()
    {
        $request = new Request();
        $createTaskRequest = \Mockery::mock(CreateTaskRequest::class);

        $createTaskRequestFactory = \Mockery::mock(CreateTaskRequestFactory::class);
        $createTaskRequestFactory
            ->shouldReceive('create')
            ->with($request)
            ->andReturn($createTaskRequest);

        $valueResolver = new CreateTaskRequestValueResolver($createTaskRequestFactory);

        $generator = $valueResolver->resolve($request, \Mockery::mock(ArgumentMetadata::class));

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertSame($createTaskRequest, $generator->current());
    }
}
