<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Resolver;

use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Infrastructure\Factory\InvalidEventFactory;
use App\Module\EventRecognition\Infrastructure\Factory\UnprocessableEventFactory;
use App\Module\EventRecognition\Infrastructure\Resolver\EventResolver;
use PHPUnit\Framework\TestCase;

final class EventResolverTest extends TestCase
{
    private EventResolver $service;
    private EventModelFactoryInterface $factory;

    public function setUp(): void
    {
        parent::setUp();

        $this->factory = $this->createMock(EventModelFactoryInterface::class);
        $this->service = new EventResolver([$this->factory]);
    }

    public function testItSuccessfullyResolvesEvent(): void
    {
        $message = new MessageModel();
        $model = $this->createMock(EventModelInterface::class);

        $this->factory
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(true)
        ;

        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with($message)
            ->willReturn($model)
        ;

        $this->assertEquals(
            $model,
            $this->service->resolve($message)
        );
    }

    public function testItCreatesInvalidEvent(): void
    {
        $message = new MessageModel();

        $this->factory
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(false)
        ;

        $this->assertEquals(
            InvalidEventFactory::createWithFault('Unsupported event type'),
            $this->service->resolve($message)
        );
    }

    public function testItCreatesUnprocessableEvent(): void
    {
        $message = new MessageModel(123);
        $exceptionMessage = 'Exception';
        $exception = new \Exception($exceptionMessage);

        $this->factory
            ->expects($this->once())
            ->method('supports')
            ->with($message)
            ->willReturn(true)
        ;

        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with($message)
            ->willThrowException($exception)
        ;

        $this->assertEquals(
            UnprocessableEventFactory::createFromException(
                $message,
                $exception
            ),
            $this->service->resolve($message)
        );
    }
}
