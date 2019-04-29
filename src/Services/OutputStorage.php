<?php

namespace App\Services;

use App\Entity\Output as OutputEntity;
use App\Model\Output as OutputModel;
use App\Model\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parser\Parser as ContentTypeParser;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class OutputStorage
{
    private $entityManager;
    private $repository;
    private $contentTypeParser;

    public function __construct(EntityManagerInterface $entityManager, ContentTypeParser $contentTypeParser)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(OutputEntity::class);
        $this->contentTypeParser = $contentTypeParser;
    }

    public function persist(OutputInterface $output): OutputEntity
    {
        $entity = OutputEntity::create(
            (string) gzdeflate($output->getContent()),
            $this->createContentType($output->getContentType()),
            $output->getErrorCount(),
            $output->getWarningCount()
        );

        $existingEntity = $this->repository->findOneBy([
            'hash' => $entity->getHash(),
        ]);

        if ($existingEntity) {
            $entity = $existingEntity;
        } else {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $entity;
    }

    public function retrieve(int $outputId): ?OutputInterface
    {
        $entity = $this->repository->find($outputId);

        if (!$entity instanceof OutputEntity) {
            return null;
        }

        return OutputModel::create(
            (string) gzinflate($entity->getContent()),
            $this->createContentType($entity->getContentType()),
            $entity->getErrorCount(),
            $entity->getWarningCount()
        );
    }

    private function createContentType(string $contentTypeString): InternetMediaTypeInterface
    {
        $contentType = new InternetMediaType('text', 'plain');
        try {
            $contentType = $this->contentTypeParser->parse($contentTypeString);
        } catch (\Exception $exception) {
        }

        return $contentType;
    }
}
