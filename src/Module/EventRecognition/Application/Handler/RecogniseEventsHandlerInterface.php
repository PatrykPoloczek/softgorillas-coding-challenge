<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Handler;

use App\Module\EventRecognition\Application\Model\ProcessSummary;

interface RecogniseEventsHandlerInterface
{
    public function handle(
        string $input,
        ?string $outputType = null
    ): ProcessSummary;
}
