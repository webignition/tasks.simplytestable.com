<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(options={"collate"="utf8_unicode_ci", "charset"="utf8"})
 */
class TaskType implements \JsonSerializable
{
    const TYPE_HTML_VALIDATION = 'html validation';
    const TYPE_CSS_VALIDATION = 'css validation';
    const TYPE_URL_DISCOVERY = 'url discovery';
    const TYPE_LINK_INTEGRITY = 'link integrity';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=false)
     */
    private $name;

    public static function create(string $name): TaskType
    {
        $taskType = new TaskType();
        $taskType->name = $name;

        return $taskType;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): string
    {
        return $this->name;
    }
}
