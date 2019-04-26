<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\ArgumentResolver;

use App\ArgumentResolver\CreateTaskCollectionRequestValueResolver;
use App\Request\CreateTaskCollectionRequest;
use App\Services\RequestFactory\CreateTaskCollectionRequestFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class CreateTaskCollectionRequestValueResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider supportsDataProvider
     */
    public function testSupports(string $argumentType, bool $expectedSupports)
    {
        $valueResolver = new CreateTaskCollectionRequestValueResolver(
            \Mockery::mock(CreateTaskCollectionRequestFactory::class)
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
                'argumentType' => CreateTaskCollectionRequest::class,
                'expectedSupports' => true,
            ],
        ];
    }

    public function testResolve()
    {
        $request = new Request();
        $createTaskCollectionRequest = \Mockery::mock(CreateTaskCollectionRequest::class);

        $createTaskCollectionRequestFactory = \Mockery::mock(CreateTaskCollectionRequestFactory::class);
        $createTaskCollectionRequestFactory
            ->shouldReceive('create')
            ->with($request)
            ->andReturn($createTaskCollectionRequest);

        $valueResolver = new CreateTaskCollectionRequestValueResolver($createTaskCollectionRequestFactory);

        $generator = $valueResolver->resolve($request, \Mockery::mock(ArgumentMetadata::class));

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertSame($createTaskCollectionRequest, $generator->current());
    }
}
