<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class InputFileNotFoundException extends \RuntimeException
{
    public static function create(string $path): self
    {
        return new self(
            sprintf(
                'Input file "%s" not found',
                $path
            )
        );
    }
}
