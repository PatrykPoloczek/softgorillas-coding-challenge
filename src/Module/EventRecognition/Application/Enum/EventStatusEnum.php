<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Enum;

use App\Module\EventRecognition\Application\Exception\UnsupportedEventStatusException;

enum EventStatusEnum: string
{
    case NEW = 'nowy';
    case PLANNED = 'zaplanowano';
    case DATE = 'termin';

    public static function resolveByValue(?string $value): self
    {
        return match ($value) {
            self::NEW->value => self::NEW,
            self::PLANNED->value => self::PLANNED,
            self::DATE->value => self::DATE,
            default => throw UnsupportedEventStatusException::create($value),
        };
    }
}
