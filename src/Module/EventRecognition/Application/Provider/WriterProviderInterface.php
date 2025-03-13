<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Provider;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Writer\WriterInterface;

interface WriterProviderInterface
{
    public function provide(OutputTypeEnum $outputType): WriterInterface;
}
