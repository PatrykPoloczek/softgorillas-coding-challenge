<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;

use function sprintf;

final class WriterNotFoundException extends \RuntimeException
{
    public static function create(OutputTypeEnum $outputType): self
    {
        return new self(
            sprintf(
                'Writer supporting outputType %s not found.',
                $outputType->value
            )
        );
    }
}
