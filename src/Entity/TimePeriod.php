<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TimePeriod implements \JsonSerializable
{
    const FORMAT = \DateTime::ATOM;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startDateTime;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDateTime;

    public static function create(\DateTime $startDateTime): TimePeriod
    {
        $timePeriod = new TimePeriod();
        $timePeriod->startDateTime = $startDateTime;

        return $timePeriod;
    }

    public function setEndDateTime(\DateTime $endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    public function jsonSerialize(): array
    {
        $timePeriodData = [
            'start_date_time' => $this->startDateTime->format(self::FORMAT),
            'end_date_time' => $this->endDateTime ? $this->endDateTime->format(self::FORMAT) : null,
        ];

        return $timePeriodData;
    }
}
