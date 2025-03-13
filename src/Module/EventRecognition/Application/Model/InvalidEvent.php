<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

final readonly class InvalidEvent implements EventModelInterface
{
    public const string SUMMARY_LABEL = 'invalid';

    public function __construct(
        private string $message
    ) {
    }

    public function getSummaryLabel(): string
    {
        return self::SUMMARY_LABEL;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }

    public function getOutputFilename(): string
    {
        return self::SUMMARY_LABEL;
    }
}
