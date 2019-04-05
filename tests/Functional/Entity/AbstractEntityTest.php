<?php

namespace App\Tests\Functional\Entity;

use App\Tests\Functional\AbstractBaseTestCase;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityTest extends AbstractBaseTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::$container->get(EntityManagerInterface::class);
    }
}
