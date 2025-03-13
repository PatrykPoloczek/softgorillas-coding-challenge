<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Resolver;

use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Application\Resolver\EventResolverInterface;
use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Infrastructure\Factory\InvalidEventFactory;
use App\Module\EventRecognition\Infrastructure\Factory\UnprocessableEventFactory;

final readonly class EventResolver implements EventResolverInterface
{
    /**
     * @var array<int, EventModelFactoryInterface> $factories
     */
    public function __construct(
        private array $factories = []
    ) {
    }

    public function resolve(
        MessageModel $message,
        array $additionalData = []
    ): EventModelInterface {
        foreach ($this->factories as $factory) {
            if ($factory->supports($message)) {
                try {
                    return $factory->create($message, $additionalData);
                } catch (\Throwable $exception) {
                    return UnprocessableEventFactory::createFromException(
                        $message,
                        $exception
                    );
                }
            }
        }

        return InvalidEventFactory::createWithFault('Unsupported event type');
    }
}
