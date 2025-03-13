<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

use function array_key_exists;

class ProcessSummary
{
    public const string TOTAL = 'total';
    public const string INVALID  = 'invalid';

    /**
     * @param array<string, int|array<int, ProcessSummaryEntry>> $summary
     */
    public function __construct(
        private array $summary = [self::TOTAL => 0]
    ) {
    }

    public function incrementTotal(): void
    {
        $this->summary[self::TOTAL]++;
    }

    public function incrementInvalid(): void
    {
        $this->summary[self::INVALID]++;
    }

    public function record(
        int $number,
        string $label,
        string $message
    ): void {
        if (!array_key_exists($label, $this->summary)) {
            $this->summary[$label] = [];
        }

        $this->summary[$label][] = new ProcessSummaryEntry(
            $number,
            $message
        );
    }

    public function getSummary(): array
    {
        return $this->summary;
    }
}
