<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Locator;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Exception\ReaderNotFoundException;
use App\Module\EventRecognition\Application\Locator\ReaderLocatorInterface;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

final readonly class ReaderLocator implements ReaderLocatorInterface
{
    public function __construct(
        #[AutowireLocator(
            services: 'event_recognition.reader',
            indexAttribute: 'key',
            defaultIndexMethod: 'getSupportedType'
        )]
        private ContainerInterface $container
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReaderNotFoundException
     */
    public function get(InputTypeEnum $inputType): ReaderInterface
    {
        if (!$this->container->has($inputType->value)) {
            throw ReaderNotFoundException::create($inputType);
        }

        /** @var ReaderInterface */
        return $this->container->get($inputType->value);
    }
}
