<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Provider;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Locator\WriterLocatorInterface;
use App\Module\EventRecognition\Application\Provider\WriterProviderInterface;
use App\Module\EventRecognition\Application\Writer\WriterInterface;

final readonly class WriterProvider implements WriterProviderInterface
{
    public function __construct(
        private WriterLocatorInterface $locator
    ) {
    }

    public function provide(OutputTypeEnum $outputType): WriterInterface
    {
        return $this->locator->get($outputType);
    }
}
