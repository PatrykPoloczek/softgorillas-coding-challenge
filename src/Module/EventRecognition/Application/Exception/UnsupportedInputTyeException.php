<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class UnsupportedInputTyeException extends \RuntimeException
{
    public static function create(string $filepath): self
    {
        return new self(
            sprintf(
                'Could not determine supported input type for file: %s.',
                $filepath
            )
        );
    }
}
