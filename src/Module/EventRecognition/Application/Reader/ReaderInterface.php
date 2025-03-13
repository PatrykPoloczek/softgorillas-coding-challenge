<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Reader;

interface ReaderInterface
{
    public function read(string $filepath): iterable;
}
