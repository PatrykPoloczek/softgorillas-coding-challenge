<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Enum;

use App\Module\EventRecognition\Application\Exception\UnsupportedInputTyeException;
use App\Module\EventRecognition\Infrastructure\Model\InputFile;

enum InputTypeEnum: string
{
    case JSON =  'json';
    case XML =  'xml';

    public static function resolveFromFile(InputFile $file): self
    {
        return match ($file->getMimeType()) {
            'application/json' => self::JSON,
            'application/xml', 'text/xml' => self::XML,
            default => throw UnsupportedInputTyeException::create($file->getFilepath())
        };
    }
}
