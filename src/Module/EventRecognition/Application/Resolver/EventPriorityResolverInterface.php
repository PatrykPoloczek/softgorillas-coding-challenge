<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Resolver;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;

interface EventPriorityResolverInterface
{
    public function resolve(string $description): EventPriorityEnum;
}
