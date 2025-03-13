<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Tracker;

use App\Module\EventRecognition\Application\Exception\UnsupportedEventTypeException;
use App\Module\EventRecognition\Application\Model\EmergencyEvent;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\InspectionEvent;
use App\Module\EventRecognition\Application\Model\InvalidEvent;
use App\Module\EventRecognition\Application\Model\ProcessSummary;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;
use App\Module\EventRecognition\Application\Tracker\ProcessTrackerInterface;

final readonly class InMemoryProcessTracker implements ProcessTrackerInterface
{
    private const string SUCCESS = 'Successfully processed.';

    public function __construct(
        private ProcessSummary $processSummary = new ProcessSummary()
    ) {
    }

    public function track(EventModelInterface $model, bool $appended = false): void
    {
        $this->processSummary->incrementTotal();
        $type = $model::class;

        match ($type) {
            InspectionEvent::class, EmergencyEvent::class => $this->handleProcessable($model, $appended),
            UnprocessableEvent::class => $this->handleUnprocessable($model),
            InvalidEvent::class => $this->handleInvalid($model),
            default => UnsupportedEventTypeException::create($type),
        };
    }

    public function getSummary(): ProcessSummary
    {
        return $this->processSummary;
    }

    private function handleProcessable(EventModelInterface $model, bool $appended): void
    {
        if (!$appended) {
            return;
        }

        /** @var InspectionEvent|EmergencyEvent $model */
        $this->processSummary->record(
            $model->getNumber(),
            $model->getSummaryLabel(),
            self::SUCCESS
        );
    }

    private function handleUnprocessable(EventModelInterface $model): void
    {
        /** @var UnprocessableEvent $model */
        $this->processSummary->record(
            $model->getNumber(),
            $model->getSummaryLabel(),
            $model->getExplanation()
        );
    }

    private function handleInvalid(EventModelInterface $model): void
    {
        $this->processSummary->incrementInvalid();
    }
}
