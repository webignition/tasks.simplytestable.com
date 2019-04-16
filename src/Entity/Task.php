<?php

namespace App\Entity;

use App\Exception\TaskMutationException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Task implements \JsonSerializable
{
    const STATE_NEW = 'new';
    const STATE_PREPARING = 'preparing';
    const STATE_PREPARED = 'prepared';
    const STATE_QUEUED = 'queued';
    const STATE_IN_PROGRESS = 'in-progress';
    const STATE_COMPLETED = 'completed';
    const STATE_CANCELLED = 'cancelled';
    const STATE_FAILED = 'failed';
    const STATE_SKIPPED = 'skipped';
    const DATE_FORMAT = \DateTime::ATOM;

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
     * @ORM\Column(type="string", unique=true, nullable=true, options={"collation"="latin1_bin"})
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, nullable=true, options={"collation"="latin1_bin"})
     */
    private $jobIdentifier;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false, options={"collation"="utf8_unicode_ci"})
     */
    private $url;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\State")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $state;

    /**
     * @var TaskType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskType")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDateTime;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDateTime;

    /**
     * @var ?Output
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Output", cascade={"persist"})
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false)
     */
    private $parameters;

    public static function create(
        string $jobIdentifier,
        string $url,
        State $state,
        TaskType $type,
        string $parameters
    ): Task {
        $task = new Task();

        $task->jobIdentifier = $jobIdentifier;
        $task->url = $url;
        $task->state = $state;
        $task->type = $type;
        $task->parameters = $parameters;

        return $task;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getJobIdentifier(): string
    {
        return $this->jobIdentifier;
    }

    public function getState(): State
    {
        return $this->state;
    }

    public function setState(State $state)
    {
        $this->state = $state;
    }

    /**
     * @param \DateTime $startDateTime
     *
     * @throws TaskMutationException
     */
    public function setStartDateTime(\DateTime $startDateTime)
    {
        if (!empty($this->startDateTime)) {
            throw TaskMutationException::createStartDateTimeAlreadySetException();
        }

        $this->startDateTime = $startDateTime;
    }

    /**
     * @param \DateTime $endDateTime
     *
     * @throws TaskMutationException
     */
    public function setEndDateTime(\DateTime $endDateTime)
    {
        if (empty($this->startDateTime)) {
            throw TaskMutationException::createStartDateTimeNotSetException();
        }

        $this->endDateTime = $endDateTime;
    }

    public function setOutput(Output $output)
    {
        $this->output = $output;
    }

    public function jsonSerialize(): array
    {
        $startDateTime = $this->startDateTime instanceof \DateTime
            ? $this->startDateTime->format(self::DATE_FORMAT)
            : null;

        $endDateTime = $this->endDateTime instanceof \DateTime
            ? $this->endDateTime->format(self::DATE_FORMAT)
            : null;

        $output = $this->output instanceof Output
            ? $this->output->jsonSerialize()
            : null;

        return [
            'id' => $this->identifier,
            'job_id' => $this->jobIdentifier,
            'url' => $this->url,
            'type' => $this->type->jsonSerialize(),
            'state' => $this->state->jsonSerialize(),
            'start_date_time' => $startDateTime,
            'end_date_time' => $endDateTime,
            'parameters' => $this->parameters,
            'output' => $output,
        ];
    }
}
