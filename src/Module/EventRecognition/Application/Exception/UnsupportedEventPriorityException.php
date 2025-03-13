<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class UnsupportedEventPriorityException extends \RuntimeException
{
    public static function create(?string $value): self
    {
        return new self(
            sprintf(
                'Unsupported event priority value: %s.',
                $value ?? 'null'
            )
        );
    }
}
