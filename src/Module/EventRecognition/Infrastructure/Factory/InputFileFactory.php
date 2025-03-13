<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Exception\FileNotReadableException;
use App\Module\EventRecognition\Application\Exception\InputFileNotFoundException;
use App\Module\EventRecognition\Application\Factory\InputFileFactoryInterface;
use App\Module\EventRecognition\Infrastructure\Model\InputFile;

use function is_file;
use function file_exists;
use function is_readable;
use function mime_content_type;
use function basename;

final readonly class InputFileFactory implements InputFileFactoryInterface
{
    public function create(string $filepath): InputFile
    {
        $this->verifyFile($filepath);
        $mimeType = mime_content_type($filepath);

        return new InputFile(
            basename($filepath),
            $filepath,
            false === $mimeType ? null : $mimeType
        );
    }

    private function verifyFile(string $filepath): void
    {
        if (!is_file($filepath) || !file_exists($filepath)) {
            throw InputFileNotFoundException::create($filepath);
        }

        if (!is_readable($filepath)) {
            throw FileNotReadableException::create($filepath);
        }
    }
}