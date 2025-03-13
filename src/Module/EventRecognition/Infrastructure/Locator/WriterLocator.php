<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Locator;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Exception\WriterNotFoundException;
use App\Module\EventRecognition\Application\Locator\WriterLocatorInterface;
use App\Module\EventRecognition\Application\Writer\WriterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final readonly class WriterLocator implements WriterLocatorInterface
{
    public function __construct(
        #[AutowireLocator(
            services: 'event_recognition.writer',
            indexAttribute: 'key',
            defaultIndexMethod: 'getSupportedType'
        )]
        private ContainerInterface $container
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws WriterNotFoundException
     */
    public function get(OutputTypeEnum $outputType): WriterInterface
    {
        if (!$this->container->has($outputType->value)) {
            throw WriterNotFoundException::create($outputType);
        }

        /** @var WriterInterface */
        return $this->container->get($outputType->value);
    }
}