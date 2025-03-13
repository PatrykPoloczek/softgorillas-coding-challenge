<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Model;

interface EventModelInterface
{
    public function getSummaryLabel(): string;
    public function getOutputFilename(): string;
    public function toArray(): array;
}
