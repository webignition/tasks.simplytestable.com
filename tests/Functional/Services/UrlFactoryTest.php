<?php

namespace App\Tests\Functional\Services;

use App\Entity\Url;
use App\Services\UrlFactory;
use App\Tests\Functional\AbstractBaseTestCase;
use App\Tests\Services\ObjectReflector;

class UrlFactoryTest extends AbstractBaseTestCase
{
    /**
     * @var UrlFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = self::$container->get(UrlFactory::class);
    }

    public function testCreate()
    {
        $urlString1 = 'http://example.com/1';
        $urlString2 = 'http://example.com/2';

        $url1 = $this->factory->create($urlString1);
        $url2 = $this->factory->create($urlString2);
        $url3 = $this->factory->create($urlString1);

        $this->assertInstanceOf(Url::class, $url1);
        $this->assertNotNull(ObjectReflector::getProperty($url1, 'id'));
        $this->assertEquals($urlString1, (string) $url1);

        $this->assertInstanceOf(Url::class, $url2);
        $this->assertNotNull(ObjectReflector::getProperty($url2, 'id'));
        $this->assertEquals($urlString2, (string) $url2);

        $this->assertSame($url1, $url3);
    }
}
