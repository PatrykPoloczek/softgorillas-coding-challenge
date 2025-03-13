<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Model;

readonly class InputFile
{
    public function __construct(
        private string $name,
        private string $filepath,
        private ?string $mimeType = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
}
