<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Resolver;

use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;

interface EventResolverInterface
{
    public function resolve(MessageModel $message, array $additionalData = []): EventModelInterface;
}
