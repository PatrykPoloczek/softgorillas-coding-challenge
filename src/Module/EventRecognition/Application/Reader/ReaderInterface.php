<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Reader;

use App\Module\EventRecognition\Infrastructure\Model\InputFile;

interface ReaderInterface
{
    public function read(InputFile $inputFile): iterable;
    public static function getSupportedType(): string;
}
