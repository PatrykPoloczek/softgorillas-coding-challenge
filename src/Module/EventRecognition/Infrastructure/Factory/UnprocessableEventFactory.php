<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;

final readonly class UnprocessableEventFactory implements EventModelFactoryInterface
{
    private const string DEFAULT_FAULT = 'Description field empty.';

    public function supports(MessageModel $message): bool
    {
        return null === $message->getDescription();
    }

    public function create(
        MessageModel $message,
        array $additionalData = []
    ): EventModelInterface {
        return new UnprocessableEvent(
            $message->getNumber(),
            self::DEFAULT_FAULT
        );
    }

    public static function createFromException(
        MessageModel $messageModel,
        \Throwable $exception
    ): EventModelInterface {
        return new UnprocessableEvent(
            $messageModel->getNumber(),
            $exception->getMessage()
        );
    }
}
