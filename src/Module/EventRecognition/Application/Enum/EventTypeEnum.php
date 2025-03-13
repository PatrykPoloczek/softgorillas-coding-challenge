<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Enum;

use App\Module\EventRecognition\Application\Exception\UnsupportedEventTypeException;

enum EventTypeEnum: string
{
    case EMERGENCY = 'zgloszenia awarii';
    case INSPECTION = 'przeglad';

    public static function resolveByValue(?string $value): self
    {
        return match ($value) {
            EventTypeEnum::EMERGENCY->value => self::EMERGENCY,
            EventTypeEnum::INSPECTION->value => self::INSPECTION,
            default => throw UnsupportedEventTypeException::create($value ?? 'null')
        };
    }
}
