<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Handler;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Factory\InputFileFactoryInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Application\Model\ProcessSummary;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;
use App\Module\EventRecognition\Application\Provider\ReaderProviderInterface;
use App\Module\EventRecognition\Application\Provider\WriterProviderInterface;
use App\Module\EventRecognition\Application\Reader\ReaderInterface;
use App\Module\EventRecognition\Application\Resolver\EventResolverInterface;
use App\Module\EventRecognition\Application\Tracker\ProcessTrackerInterface;
use App\Module\EventRecognition\Application\Writer\WriterInterface;
use App\Module\EventRecognition\Infrastructure\Factory\MessageModelFactory;
use App\Module\EventRecognition\Infrastructure\Handler\RecogniseEventsHandler;
use App\Module\EventRecognition\Infrastructure\Model\InputFile;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class RecogniseEventsHandlerTest extends TestCase
{
    private RecogniseEventsHandler $service;
    private ReaderProviderInterface $readerProvider;
    private EventResolverInterface $eventResolver;
    private WriterProviderInterface $writerProvider;
    private ProcessTrackerInterface $tracker;
    private LoggerInterface $logger;
    private InputFileFactoryInterface $inputFileFactory;

    public function setUp(): void
    {
        parent::setUp();

        $messageModelFactory = new MessageModelFactory();
        $this->readerProvider = $this->createMock(ReaderProviderInterface::class);
        $this->eventResolver = $this->createMock(EventResolverInterface::class);
        $this->writerProvider = $this->createMock(WriterProviderInterface::class);
        $this->tracker = $this->createMock(ProcessTrackerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->inputFileFactory = $this->createMock(InputFileFactoryInterface::class);

        $this->service = new RecogniseEventsHandler(
            $messageModelFactory,
            $this->inputFileFactory,
            $this->readerProvider,
            $this->eventResolver,
            $this->writerProvider,
            $this->tracker,
            $this->logger
        );
    }

    public function testItSuccessfullyHandlesTheProcess(): void
    {
        $outputType = OutputTypeEnum::JSON;
        $input = 'some path';
        $number = 123;
        $description = 'description';
        $dueDate = null;
        $phone = null;

        $entry = [
            'number' => $number,
            'description' => $description,
            'dueDate' => $dueDate,
            'phone' => $phone,
        ];
        $message = new MessageModel(
            $number,
            $description,
            $dueDate,
            $phone
        );
        $inputFile = new InputFile(
            'input.json',
            '/some-path/input.json',
            'application/json'
        );
        $reader = $this->createMock(ReaderInterface::class);

        $this->inputFileFactory
            ->expects($this->once())
            ->method('create')
            ->with($input)
            ->willReturn($inputFile)
        ;

        $this->readerProvider
            ->expects($this->once())
            ->method('provide')
            ->with()
            ->willReturn($reader)
        ;

        $event = new UnprocessableEvent($number, '');
        $writer = $this->createMock(WriterInterface::class);
        $this->writerProvider
            ->expects($this->once())
            ->method('provide')
            ->with($outputType)
            ->willReturn($writer)
        ;

        $reader
            ->expects($this->once())
            ->method('read')
            ->with($inputFile)
            ->willReturn([$entry])
        ;

        $this->eventResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($message)
            ->willReturn($event)
        ;

        $writer
            ->expects($this->once())
            ->method('append')
            ->with($event)
            ->willReturn(true)
        ;

        $this->tracker
            ->expects($this->once())
            ->method('track')
            ->with($event, true)
        ;

        $writer
            ->expects($this->once())
            ->method('commit')
        ;

        $this->logger
            ->expects($this->once())
            ->method('info')
        ;

        $result = $this->service->handle($input, $outputType->value);
        $this->assertInstanceOf(ProcessSummary::class, $result);
    }
}
