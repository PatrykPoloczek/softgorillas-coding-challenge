<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Enum;

use App\Module\EventRecognition\Application\Exception\UnsupportedOutputTypeException;
use function strtolower;

enum OutputTypeEnum: string
{
    case JSON = 'json';
    case XML = 'xml';

    public static function resolveFromValue(string $value): self
    {
        return match (strtolower($value)) {
            self::JSON->value => self::JSON,
            self::XML->value => self::XML,
            default => throw UnsupportedOutputTypeException::create($value)
        };
    }
}
