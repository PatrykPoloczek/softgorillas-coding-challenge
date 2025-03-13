<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

use Throwable;

final class FileReadingFailedException extends \RuntimeException
{
    public static function fromPrevious(
        string $filepath,
        Throwable $previous
    ): self {
        return new self(
            message: sprintf(
                'Failed to read file "%s"',
                $filepath
            ),
            previous: $previous
        );
    }
}
