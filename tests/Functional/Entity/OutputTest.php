<?php

namespace App\Tests\Functional\Entity;

use App\Entity\Output;
use App\Tests\Functional\AbstractBaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use webignition\InternetMediaType\InternetMediaType;

class OutputTest extends AbstractBaseTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }

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
