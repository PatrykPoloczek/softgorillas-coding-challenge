<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Tracker;

use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\ProcessSummary;

interface ProcessTrackerInterface
{
    public function track(EventModelInterface $model, bool $appended = false): void;
    public function getSummary(): ProcessSummary;
}
