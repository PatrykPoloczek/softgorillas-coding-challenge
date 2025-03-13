<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Provider;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;

interface ReaderProviderInterface
{
    public function provide(InputTypeEnum $inputType): ReaderInterface;
}