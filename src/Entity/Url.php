<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Psr\Http\Message\UriInterface;
use webignition\Uri\Normalizer;
use webignition\Uri\Uri;

/**
 * @ORM\Entity
 */
class Url implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var UriInterface|string
     *
     * @ORM\Column(type="text", nullable=false, options={"collation"="utf8_unicode_ci"})
     */
    private $uri;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=false, length=32)
     */
    private $hash;

    public static function create(UriInterface $uri): Url
    {
        $url = new Url();
        $url->uri = Normalizer::normalize($uri);
        $url->hash = md5((string) $uri);

        return $url;
    }

    public function getUri(): UriInterface
    {
        if (is_string($this->uri)) {
            $this->uri = new Uri($this->uri);
        }

        return $this->uri;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function __toString()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): string
    {
        return (string) $this->uri;
    }
}
