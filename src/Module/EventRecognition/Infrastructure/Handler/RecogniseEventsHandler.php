<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Handler;

use App\Module\EventRecognition\Application\Enum\InputTypeEnum;
use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Factory\InputFileFactoryInterface;
use App\Module\EventRecognition\Application\Provider\ReaderProviderInterface;
use App\Module\EventRecognition\Application\Provider\WriterProviderInterface;
use App\Module\EventRecognition\Application\Resolver\EventResolverInterface;
use App\Module\EventRecognition\Application\Handler\RecogniseEventsHandlerInterface;
use App\Module\EventRecognition\Application\Tracker\ProcessTrackerInterface;
use App\Module\EventRecognition\Infrastructure\Factory\MessageModelFactory;
use App\Module\EventRecognition\Application\Model\ProcessSummary;
use Psr\Log\LoggerInterface;

use function sprintf;

final readonly class RecogniseEventsHandler implements RecogniseEventsHandlerInterface
{
    private const int NOTIFY_THRESHOLD = 10;

    public function __construct(
        private MessageModelFactory $messageModelFactory,
        private InputFileFactoryInterface $inputFileFactory,
        private ReaderProviderInterface $readerProvider,
        private EventResolverInterface $eventResolver,
        private WriterProviderInterface $writerProvider,
        private ProcessTrackerInterface $tracker,
        private LoggerInterface $logger
    ) {
    }

    public function handle(
        string $input,
        ?string $outputType = null
    ): ProcessSummary {
        $inputFile = $this->inputFileFactory->create($input);
        $inputType = InputTypeEnum::resolveFromFile($inputFile);
        $outputType = OutputTypeEnum::resolveFromValue($outputType);
        $writer = $this->writerProvider->provide($outputType);
        $reader = $this->readerProvider->provide($inputType);

        foreach ($reader->read($inputFile) as $index => $entry) {
            $this->notify($index);
            $message = $this->messageModelFactory->create($entry);
            $event = $this->eventResolver->resolve($message, $entry);
            $appended = $writer->append($event);
            $this->tracker->track($event, $appended);
        }

        $writer->commit();

        return $this->tracker->getSummary();
    }

    private function notify(int $index): void
    {
        if ($index % self::NOTIFY_THRESHOLD !== 0) {
            return;
        }

        $this->logger->info(
            sprintf(
                "Currently processed message identifier - %d.",
                ++$index
            )
        );
    }
}
