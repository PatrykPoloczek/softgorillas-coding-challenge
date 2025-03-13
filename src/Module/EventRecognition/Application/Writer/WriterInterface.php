<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Writer;

use App\Module\EventRecognition\Application\Model\EventModelInterface;

interface WriterInterface
{
    public function append(EventModelInterface $event): bool;
    public function commit(): void;
    public static function getSupportedType(): string;
}
