<?php

namespace App\Tests\Functional\Services;

use App\Entity\State;
use App\Services\StateLoader;
use App\Services\StateNames;
use App\Tests\Functional\AbstractBaseTestCase;

class StateLoaderTest extends AbstractBaseTestCase
{
    /**
     * @var StateLoader
     */
    private $stateLoader;

    /**
     * @var string[]
     */
    private $stateNames;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateLoader = self::$container->get(StateLoader::class);

        $stateNames = self::$container->get(StateNames::class);
        $this->stateNames = $stateNames->getData();
    }

    public function testLoadKnown()
    {
        foreach ($this->stateNames as $stateName) {
            $state = $this->stateLoader->load($stateName);

            $this->assertInstanceOf(State::class, $state);
        }
    }

    public function testLoadUnknown()
    {
        $this->assertNull($this->stateLoader->load('foo'));
    }
}
