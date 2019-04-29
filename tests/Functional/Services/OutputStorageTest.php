<?php

namespace App\Tests\Functional\Services;

use App\Entity\Output as OutputEntity;
use App\Model\Output as OutputModel;
use App\Model\OutputInterface;
use App\Services\OutputStorage;
use App\Tests\Functional\AbstractBaseTestCase;
use Doctrine\ORM\EntityManagerInterface;
use webignition\InternetMediaType\InternetMediaType;

class OutputStorageTest extends AbstractBaseTestCase
{
    /**
     * @var OutputStorage
     */
    private $outputStorage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputStorage = self::$container->get(OutputStorage::class);
    }

    public function testCreatePersistRetrieve()
    {
        $entityManager = self::$container->get(EntityManagerInterface::class);

        $content = '[{}]';
        $contentType = new InternetMediaType('application', 'json');
        $errorCount = 1;
        $warningCount = 2;

        $outputModel = OutputModel::create($content, $contentType, $errorCount, $warningCount);

        $this->assertInstanceOf(OutputInterface::class, $outputModel);
        $this->assertInstanceOf(OutputModel::class, $outputModel);

        $outputEntity = $this->outputStorage->persist($outputModel);

        $this->assertInstanceOf(OutputInterface::class, $outputEntity);
        $this->assertInstanceOf(OutputEntity::class, $outputEntity);

        $id = (int) $outputEntity->getId();

        $entityManager->close();

        $retrievedOutputModel = $this->outputStorage->retrieve($id);

        $this->assertInstanceOf(OutputInterface::class, $retrievedOutputModel);
        $this->assertInstanceOf(OutputModel::class, $retrievedOutputModel);

        if ($retrievedOutputModel instanceof OutputInterface) {
            $this->assertEquals($content, $retrievedOutputModel->getContent());
            $this->assertEquals($contentType, $retrievedOutputModel->getContentType());
            $this->assertEquals($errorCount, $retrievedOutputModel->getErrorCount());
            $this->assertEquals($warningCount, $retrievedOutputModel->getWarningCount());
        }
    }
}
