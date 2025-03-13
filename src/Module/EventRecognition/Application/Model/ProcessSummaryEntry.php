<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

readonly class ProcessSummaryEntry
{
    public function __construct(
        private int $number,
        private string $message
    ) {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
