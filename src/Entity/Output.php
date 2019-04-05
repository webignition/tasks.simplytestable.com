<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(name="hash_idx", columns={"hash"})
 *     }
 * )
 */
class Output implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $contentType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $errorCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $warningCount = 0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=32)
     */
    private $hash;

    public static function create(
        string $content,
        InternetMediaTypeInterface $contentType,
        int $errorCount,
        int $warningCount
    ): Output {
        $output = new Output();
        $output->content = $content;
        $output->contentType = (string) $contentType;
        $output->errorCount = $errorCount;
        $output->warningCount = $warningCount;
        $output->hash = md5((string) json_encode($output));

        return $output;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getWarningCount(): int
    {
        return $this->warningCount;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function jsonSerialize(): array
    {
        return [
            'content' => $this->content,
            'content_type' => (string)$this->contentType,
            'error_count' => $this->errorCount,
            'warning_count' => $this->warningCount,
        ];
    }
}
