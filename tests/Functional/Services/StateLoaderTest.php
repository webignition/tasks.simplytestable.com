<?php

namespace App\Tests\Functional\Services;

use App\Entity\State;
use App\Services\StateLoader;
use App\Services\StatesDataProvider;
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

        $statesDataProvider = self::$container->get(StatesDataProvider::class);
        $this->stateNames = $statesDataProvider->getData();
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
