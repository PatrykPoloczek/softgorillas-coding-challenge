<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Locator;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Writer\WriterInterface;

interface WriterLocatorInterface
{
    public function get(OutputTypeEnum $outputType): WriterInterface;
}
