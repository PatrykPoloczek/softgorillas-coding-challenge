<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Resolver;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;
use App\Module\EventRecognition\Application\Resolver\EventPriorityResolverInterface;

final readonly class EmergencyEventPriorityResolver implements EventPriorityResolverInterface
{
    private const string CRITICAL_EVENT_PRIORITY_PHRASE = 'bardzo pilne';
    private const string URGENT_EVENT_PRIORITY_PHRASE = 'pilne';

    public function resolve(string $description): EventPriorityEnum
    {
        $description = strtolower($description);

        return match (true) {
            str_contains($description, self::CRITICAL_EVENT_PRIORITY_PHRASE) => EventPriorityEnum::CRITICAL,
            str_contains($description, self::URGENT_EVENT_PRIORITY_PHRASE) => EventPriorityEnum::URGENT,
            default => EventPriorityEnum::NORMAL
        };
    }
}
