<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Exception;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;

final class ReaderNotFoundException extends \RuntimeException
{
    public static function create(InputTypeEnum $inputType): self
    {
        return new self(
            sprintf(
                'Reader supporting inputType %s not found.',
                $inputType->value
            )
        );
    }
}
