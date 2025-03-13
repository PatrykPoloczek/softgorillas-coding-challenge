<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Provider;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Locator\ReaderLocatorInterface;
use App\Module\EventRecognition\Application\Provider\ReaderProviderInterface;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;

final readonly class ReaderProvider implements ReaderProviderInterface
{
    public function __construct(
        private ReaderLocatorInterface $locator
    ) {
    }

    public function provide(InputTypeEnum $inputType): ReaderInterface
    {
        return $this->locator->get($inputType);
    }
}
