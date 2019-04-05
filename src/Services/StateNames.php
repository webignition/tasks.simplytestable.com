<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class StateNames
{
    const RESOURCE = '/config/resources/states.yaml';

    /**
     * @var string
     */
    private $resourcePath;

    /**
     * @var array
     */
    private $names = [];

    public function __construct(string $kernelProjectDirectory)
    {
        $this->resourcePath = $kernelProjectDirectory . self::RESOURCE;
    }

    public function getNames()
    {
        if (empty($this->names)) {
            $this->loadNames();
        }

        return $this->names;
    }

    private function loadNames()
    {
        $stateData = Yaml::parseFile($this->resourcePath);

        foreach ($stateData as $entity => $names) {
            foreach ($names as $name) {
                $this->names[] = $entity . '-' . $name;
            }
        }
    }
}
