<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Output;
use webignition\InternetMediaType\InternetMediaType;

class OutputTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $content = 'content';
        $contentType = new InternetMediaType('application', 'json');
        $errorCount = 1;
        $warningCount = 2;

        $output = Output::create($content, $contentType, $errorCount, $warningCount);

        $this->assertInstanceOf(Output::class, $output);
        $this->assertSame($content, $output->getContent());
        $this->assertSame((string) $contentType, $output->getContentType());
        $this->assertSame($errorCount, $output->getErrorCount());
        $this->assertSame($warningCount, $output->getWarningCount());
        $this->assertRegExp('/[a-f0-9]{32}/', $output->getHash());
    }
}
