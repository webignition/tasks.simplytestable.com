<?php

namespace App\Model;

interface OutputInterface extends \JsonSerializable
{
    public function getContent(): string;
    public function getContentType(): string;
    public function getErrorCount(): int;
    public function getWarningCount(): int;
}
