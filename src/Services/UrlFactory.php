<?php

namespace App\Services;

use App\Entity\Task;
use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use webignition\Uri\Uri;

class UrlFactory
{
    const CREATION_STATE = Task::STATE_PREFIX . Task::STATE_NEW;

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Url::class);
    }

    public function create(string $urlString): Url
    {
        $url = Url::create(new Uri($urlString));

        $existingEntity = $this->repository->findOneBy([
            'hash' => $url->getHash(),
        ]);

        if ($existingEntity) {
            $url = $existingEntity;
        } else {
            $this->entityManager->persist($url);
            $this->entityManager->flush();
        }

        return $url;
    }
}
