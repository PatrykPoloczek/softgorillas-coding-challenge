<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Resolver;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;
use App\Module\EventRecognition\Infrastructure\Resolver\EmergencyEventPriorityResolver;
use PHPUnit\Framework\TestCase;

final class EmergencyEventPriorityResolverTest extends TestCase
{
    private const string CRITICAL_EVENT_PRIORITY_PHRASE = 'bardzo pilne';
    private const string URGENT_EVENT_PRIORITY_PHRASE = 'pilne';

    private EmergencyEventPriorityResolver $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new EmergencyEventPriorityResolver();
    }

    public function testResolving(): void
    {
        $this->assertEquals(
            EventPriorityEnum::CRITICAL,
            $this->service->resolve(self::CRITICAL_EVENT_PRIORITY_PHRASE)
        );

        $this->assertEquals(
            EventPriorityEnum::URGENT,
            $this->service->resolve(self::URGENT_EVENT_PRIORITY_PHRASE)
        );

        $this->assertEquals(
            EventPriorityEnum::NORMAL,
            $this->service->resolve('')
        );
    }
}
