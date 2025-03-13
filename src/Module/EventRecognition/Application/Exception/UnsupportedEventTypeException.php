<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

use function sprintf;

final class UnsupportedEventTypeException extends \RuntimeException
{
    public static function create(string $eventType): self
    {
        return new self(
            sprintf(
                'Unsupported event type "%s"',
                $eventType
            )
        );
    }
}
