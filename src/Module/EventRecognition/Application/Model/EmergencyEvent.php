<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;
use App\Module\EventRecognition\Application\Enum\EventStatusEnum;
use App\Module\EventRecognition\Application\Enum\EventTypeEnum;

final readonly class EmergencyEvent implements EventModelInterface
{
    private const string OUTPUT_FILENAME = 'zgloszenia_awarii';
    private const string SUMMARY_LABEL = 'emergencies';
    private const string DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private int $number,
        private string $description,
        private EventPriorityEnum $priority,
        private EventTypeEnum $type = EventTypeEnum::EMERGENCY,
        private ?\DateTimeInterface $date = null,
        private EventStatusEnum $status = EventStatusEnum::NEW,
        private ?string $comments = null,
        private ?string $phoneNumber = null,
        private \DateTimeInterface $createdAt = new \DateTime()
    ) {
    }

    public function getSummaryLabel(): string
    {
        return self::SUMMARY_LABEL;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): EventPriorityEnum
    {
        return $this->priority;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getStatus(): EventStatusEnum
    {
        return $this->status;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'description' => $this->description,
            'type' => $this->type,
            'priority' => $this->priority->value,
            'dueDate' => $this->date?->format(self::DATE_FORMAT) ?? '',
            'status' => $this->status->value,
            'comments' => $this->comments ?? '',
            'clientNumber' => $this->phoneNumber ?? '',
            'createdAt' => $this->createdAt?->format(self::DATE_FORMAT) ?? '',
        ];
    }

    public function getOutputFilename(): string
    {
        return self::OUTPUT_FILENAME;
    }

    public function getType(): EventTypeEnum
    {
        return $this->type;
    }
}
