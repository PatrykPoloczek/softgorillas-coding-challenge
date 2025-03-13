<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Factory;

use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;

interface EventModelFactoryInterface
{
    public function supports(MessageModel $message): bool;
    public function create(
        MessageModel $message,
        array $additionalData = []
    ): EventModelInterface;
}
