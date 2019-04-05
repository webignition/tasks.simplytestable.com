<?php

namespace App\Services;

class StateNames extends YamlResourceLoader
{
    public function getData()
    {
        $data = parent::getData();
        $names = [];

        foreach ($data as $entity => $names) {
            foreach ($names as $name) {
                $names[] = $entity . '-' . $name;
            }
        }

        return $names;
    }
}
