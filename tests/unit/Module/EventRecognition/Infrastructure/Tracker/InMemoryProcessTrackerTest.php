<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Tracker;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;
use App\Module\EventRecognition\Application\Enum\EventStatusEnum;
use App\Module\EventRecognition\Application\Model\EmergencyEvent;
use App\Module\EventRecognition\Application\Model\ProcessSummary;
use App\Module\EventRecognition\Application\Model\ProcessSummaryEntry;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;
use App\Module\EventRecognition\Infrastructure\Tracker\InMemoryProcessTracker;
use PHPUnit\Framework\TestCase;

final class InMemoryProcessTrackerTest extends TestCase
{
    private const string SUCCESS = 'Successfully processed.';

    public function testTracker(): void
    {
        $number1 = 123;
        $explanation = 'Some explanation.';
        $number2 = 345;
        $description = 'Some description.';
        $priority = EventPriorityEnum::NORMAL;
        $status = EventStatusEnum::NEW;
        $event1 = new UnprocessableEvent($number1, $explanation);
        $event2 = new EmergencyEvent(
            number: $number2,
            description: $description,
            priority: $priority,
            status: $status
        );
        $service = new InMemoryProcessTracker();
        $service->track($event1);
        $service->track($event2, true);

        $expectedSummary = new ProcessSummary([
            ProcessSummary::TOTAL => 2,
            $event1->getSummaryLabel() => [
                new ProcessSummaryEntry(
                    $number1,
                    $explanation
                ),
            ],
            $event2->getSummaryLabel() => [
                new ProcessSummaryEntry(
                    $number2,
                    self::SUCCESS
                ),
            ],
        ]);

        $this->assertEquals(
            $expectedSummary,
            $service->getSummary()
        );
    }
}
