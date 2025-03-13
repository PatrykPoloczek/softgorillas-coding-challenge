<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Locator;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;

interface ReaderLocatorInterface
{
    public function get(InputTypeEnum $inputType): ReaderInterface;
}
