<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\InvalidEvent;
use App\Module\EventRecognition\Application\Model\MessageModel;

final readonly class InvalidEventFactory implements EventModelFactoryInterface
{
    private const string DEFAULT_FAULT = 'Number field empty.';

    public function supports(MessageModel $message): bool
    {
        return null === $message->getNumber();
    }

    public function create(
        MessageModel $message,
        array $additionalData = []
    ): EventModelInterface {
        return new InvalidEvent(self::DEFAULT_FAULT);
    }

    public static function createWithFault(string $fault): EventModelInterface
    {
        return new InvalidEvent($fault);
    }
}
