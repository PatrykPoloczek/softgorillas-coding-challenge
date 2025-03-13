<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

use App\Module\EventRecognition\Application\Enum\EventStatusEnum;
use App\Module\EventRecognition\Application\Enum\EventTypeEnum;

final readonly class InspectionEvent implements EventModelInterface
{
    private const string OUTPUT_FILENAME = 'przeglady';
    private const string SUMMARY_LABEL = 'inspections';
    private const string DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private int $number,
        private string $description,
        private EventTypeEnum $type = EventTypeEnum::INSPECTION,
        private ?\DateTimeInterface $date = null,
        private ?int $weekNumber = null,
        private EventStatusEnum $status = EventStatusEnum::NEW,
        private ?string $recommendations = null,
        private ?string $phoneNumber = null,
        private \DateTimeInterface  $createdAt = new \DateTime()
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getStatus(): EventStatusEnum
    {
        return $this->status;
    }

    public function getWeekNumber(): ?int
    {
        return $this->weekNumber;
    }

    public function getRecommendations(): ?string
    {
        return $this->recommendations;
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
            'dueDate' => $this->date?->format(self::DATE_FORMAT) ?? '',
            'weekOfYear' => $this->weekNumber ?? '',
            'status' => $this->status->value ?? '',
            'recommendations' => $this->recommendations ?? '',
            'clientPhone' => $this->phoneNumber ?? '',
            'createdAt' => $this->createdAt->format(self::DATE_FORMAT) ?? '',
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
