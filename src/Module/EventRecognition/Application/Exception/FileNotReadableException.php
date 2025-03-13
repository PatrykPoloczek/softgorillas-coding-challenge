<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class FileNotReadableException extends \RuntimeException
{
    public static function create(string $filepath): self
    {
        return new self(
            sprintf(
                'File %s is not readable',
                $filepath
            )
        );
    }
}
