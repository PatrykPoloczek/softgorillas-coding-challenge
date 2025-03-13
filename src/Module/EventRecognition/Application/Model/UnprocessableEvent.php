<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

final readonly class UnprocessableEvent implements EventModelInterface
{
    public const string SUMMARY_LABEL = 'unprocessable';

    public function __construct(
        private int $number,
        private string $explanation
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

    public function getExplanation(): string
    {
        return $this->explanation;
    }

    /**
     * @return array<string, int|string>
     */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'explanation' => $this->explanation,
        ];
    }

    public function getOutputFilename(): string
    {
        return self::SUMMARY_LABEL;
    }
}
