<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Enum;

use App\Module\EventRecognition\Application\Exception\UnsupportedEventPriorityException;

enum EventPriorityEnum: string
{
    case NORMAL = 'normal';
    case URGENT = 'urgent';
    case CRITICAL = 'critical';

    public static function resolveByValue(?string $value): self
    {
        return match ($value) {
            self::NORMAL->value => self::NORMAL,
            self::URGENT->value => self::URGENT,
            self::CRITICAL->value => self::CRITICAL,
            default => throw UnsupportedEventPriorityException::create($value)
        };
    }
}
