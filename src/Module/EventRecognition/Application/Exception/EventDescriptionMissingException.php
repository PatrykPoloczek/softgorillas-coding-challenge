<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class EventDescriptionMissingException extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Event description missing.');
    }
}
