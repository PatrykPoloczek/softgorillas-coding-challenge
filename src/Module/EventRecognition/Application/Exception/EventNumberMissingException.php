<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

final class EventNumberMissingException extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Event number missing.');
    }
}
