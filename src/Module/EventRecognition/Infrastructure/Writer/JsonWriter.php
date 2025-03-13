<?php

namespace App\Module\EventRecognition\Infrastructure\Writer;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Exception\UnsupportedEventTypeException;
use App\Module\EventRecognition\Application\Model\EmergencyEvent;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\InspectionEvent;
use App\Module\EventRecognition\Application\Model\InvalidEvent;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;
use App\Module\EventRecognition\Application\Writer\WriterInterface;

use function md5;
use function array_keys;
use function time;
use function array_map;
use function array_values;

final class JsonWriter implements WriterInterface
{
    private array $data = [];

    public function __construct(
        private readonly string $storagePath
    ) {
    }

    public function append(EventModelInterface $event): bool
    {
        return match ($event::class) {
            InspectionEvent::class, EmergencyEvent::class => $this->handleDescriptionSensitive($event),
            UnprocessableEvent::class, InvalidEvent::class => true,
            default => UnsupportedEventTypeException::create($event::class),
        };
    }

    /**
     * @throws \JsonException
     */
    public function commit(): void
    {
        $timestamp = time();

        foreach ($this->data as $label => $events) {
            $file = sprintf(
                '%s/%d_%s.json',
                $this->storagePath,
                $timestamp,
                $label
            );

            file_put_contents(
                $file,
                json_encode(
                    array_values(array_map(
                        fn (EventModelInterface $event): array => $event->toArray(),
                        $events
                    )),
                    JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE
                )
            );
        }
    }

    public static function getSupportedType(): string
    {
        return OutputTypeEnum::JSON->value;
    }

    private function handleDescriptionSensitive(EmergencyEvent|InspectionEvent $event): bool
    {
        $label = $event->getOutputFilename();
        $description = md5($event->getDescription());
        $tracked = array_keys($this->data[$label] ?? []);

        if (in_array($description, $tracked)) {
            return false;
        }

        $this->data[$label][$description] = $event;

        return true;
    }
}