<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

use function sprintf;

final class UnsupportedOutputTypeException extends \RuntimeException
{
    public static function create(string $type): self
    {
        return new self(
            sprintf(
                'Unsupported output type %s',
                $type
            )
        );
    }
}
