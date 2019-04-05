<?php
/** @noinspection PhpDocSignatureInspection */

namespace App\Tests\Unit\Entity;

use App\Entity\TimePeriod;

class TimePeriodTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider jsonSerializeDataProvider
     */
    public function testJsonSerialize(TimePeriod $timePeriod, array $expectedSerializedData)
    {
        $this->assertEquals($expectedSerializedData, $timePeriod->jsonSerialize());
    }

    public function jsonSerializeDataProvider(): array
    {
        return [
            'no end date time' => [
                'timePeriod' => $this->createTimePeriod(new \DateTime('2019-04-05 15:10:00'), null),
                'expectedSerializedData' => [
                    'start_date_time' => '2019-04-05T15:10:00+00:00',
                    'end_date_time' => null,
                ],
            ],
            'start date time and end date time' => [
                'timePeriod' => $this->createTimePeriod(
                    new \DateTime('2019-04-05 15:20:00'),
                    new \DateTime('2019-04-05 15:30:00')
                ),
                'expectedSerializedData' => [
                    'start_date_time' => '2019-04-05T15:20:00+00:00',
                    'end_date_time' => '2019-04-05T15:30:00+00:00',
                ],
            ],
        ];
    }

    private function createTimePeriod(\DateTime $startDateTime, ?\DateTime $endDateTime): TimePeriod
    {
        $timePeriod = TimePeriod::create($startDateTime);

        if ($endDateTime) {
            $timePeriod->setEndDateTime($endDateTime);
        }

        return $timePeriod;
    }
}
