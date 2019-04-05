<?php

namespace App\Tests\Functional\Entity;

use App\Entity\TimePeriod;
use App\Tests\Services\ObjectReflector;

class TimePeriodTest extends AbstractEntityTest
{
    public function testCreateStartDateTimeOnly()
    {
        $timePeriod = TimePeriod::create(new \DateTime());

        $this->assertNull(ObjectReflector::getProperty($timePeriod, 'id'));
        $this->assertNotNull(ObjectReflector::getProperty($timePeriod, 'startDateTime'));
        $this->assertNull(ObjectReflector::getProperty($timePeriod, 'endDateTime'));

        $this->entityManager->persist($timePeriod);
        $this->entityManager->flush();

        $this->assertNotNull(ObjectReflector::getProperty($timePeriod, 'id'));
    }

    public function testCreateStartDateTimeAndEndDateTime()
    {
        $timePeriod = TimePeriod::create(new \DateTime());
        $timePeriod->setEndDateTime(new \DateTime());

        $this->assertNull(ObjectReflector::getProperty($timePeriod, 'id'));
        $this->assertNotNull(ObjectReflector::getProperty($timePeriod, 'startDateTime'));
        $this->assertNotNull(ObjectReflector::getProperty($timePeriod, 'endDateTime'));

        $this->entityManager->persist($timePeriod);
        $this->entityManager->flush();

        $this->assertNotNull(ObjectReflector::getProperty($timePeriod, 'id'));
    }
}
