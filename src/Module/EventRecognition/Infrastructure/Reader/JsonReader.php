<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Reader;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Exception\FileNotReadableException;
use App\Module\EventRecognition\Application\Exception\InputFileNotFoundException;
use App\Module\EventRecognition\Application\Exception\FileReadingFailedException;
use App\Module\EventRecognition\Application\Exception\MalformedJsonPayloadException;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;
use App\Module\EventRecognition\Infrastructure\Model\InputFile;

use function is_file;
use function file_exists;
use function is_readable;
use function json_validate;
use function json_decode;

final class JsonReader implements ReaderInterface
{
    private ?string $source = null;

    private function init(InputFile $inputFile): void
    {
        $filepath = $inputFile->getFilepath();

        if (!is_file($filepath) || !file_exists($filepath)) {
            throw InputFileNotFoundException::create($filepath);
        }

        if (!is_readable($filepath)) {
            throw FileNotReadableException::create($filepath);
        }

        $this->source = $filepath;
    }

    public function read(InputFile $inputFile): iterable
    {
        try {
            $this->init($inputFile);
            $content = file_get_contents($this->source);
    
            if (!json_validate($content)) {
                throw MalformedJsonPayloadException::create();
            }
    
            yield from json_decode(
                $content,
                true,
                JSON_THROW_ON_ERROR
            );
        } catch (\Throwable $exception) {
            throw FileReadingFailedException::fromPrevious($inputFile->getFilepath(), $exception);
        }
    }

    public static function getSupportedType(): string
    {
        return InputTypeEnum::JSON->value;
    }
}
