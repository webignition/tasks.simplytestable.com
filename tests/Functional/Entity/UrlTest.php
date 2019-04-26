<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Tests\Functional\Entity;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\UriInterface;
use webignition\Uri\Uri;

class UrlTest extends AbstractEntityTest
{
    public function testGetUri()
    {
        $entityManager = self::$container->get(EntityManagerInterface::class);
        $urlRepository = $entityManager->getRepository(Url::class);

        $uri = new Uri('http://example.com/');

        $url = Url::create($uri);

        $this->assertSame($uri, $url->getUri());

        $entityManager->persist($url);
        $entityManager->flush();

        $hash = $url->getHash();

        $entityManager->close();

        /* @var Url $retrievedUrl */
        $retrievedUrl = $urlRepository->findOneBy([
            'hash' => $hash,
        ]);

        $this->assertInstanceOf(Url::class, $retrievedUrl);

        if ($retrievedUrl instanceof Url) {
            $retrievedUri = $retrievedUrl->getUri();

            $this->assertInstanceOf(UriInterface::class, $retrievedUri);
            $this->assertEquals($uri, $retrievedUri);
        }
    }
}
