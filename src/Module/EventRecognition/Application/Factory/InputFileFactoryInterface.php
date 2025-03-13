<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Application\Factory;

use App\Module\EventRecognition\Infrastructure\Model\InputFile;

interface InputFileFactoryInterface
{
    public function create(string $filepath): InputFile;
}
