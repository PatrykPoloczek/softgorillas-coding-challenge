<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

final readonly class MessageModel
{
    public function __construct(
        private ?int $number = null,
        private ?string $description = null,
        private ?string $dueDate = null,
        private ?string $phone = null
    ) {
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDueDate(): ?string
    {
        return $this->dueDate;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}