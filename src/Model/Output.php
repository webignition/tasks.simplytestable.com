<?php

namespace App\Model;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class Output implements OutputInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var InternetMediaTypeInterface
     */
    private $contentType;

    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $warningCount = 0;

    public static function create(
        string $content,
        InternetMediaTypeInterface $contentType,
        int $errorCount,
        int $warningCount
    ): Output {
        $output = new Output();
        $output->content = $content;
        $output->contentType = $contentType;
        $output->errorCount = $errorCount;
        $output->warningCount = $warningCount;

        return $output;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return (string) $this->contentType;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    public function getWarningCount(): int
    {
        return $this->warningCount;
    }

    public function jsonSerialize(): array
    {
        return [
            'content' => $this->content,
            'content_type' => (string) $this->contentType,
            'error_count' => $this->errorCount,
            'warning_count' => $this->warningCount,
        ];
    }
}
