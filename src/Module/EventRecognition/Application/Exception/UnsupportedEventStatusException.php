<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class UnsupportedEventStatusException extends \RuntimeException
{
    public static function create(?string $status): self
    {
        return new self(
            sprintf(
                'Unsupported event status value: %s.',
                $value ?? 'null'
            )
        );
    }
}
