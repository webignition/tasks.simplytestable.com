<?php

namespace App\Tests\Functional\Entity;

use App\Entity\Output;
use webignition\InternetMediaType\InternetMediaType;

class OutputTest extends AbstractEntityTest
{
    public function testCreate()
    {
        $content = 'content';
        $contentType = new InternetMediaType('application', 'json');
        $errorCount = 1;
        $warningCount = 2;

        $output = Output::create($content, $contentType, $errorCount, $warningCount);

        $this->assertNull($output->getId());

        $this->entityManager->persist($output);
        $this->entityManager->flush();

        $this->assertNotNull($output->getId());
    }
}
