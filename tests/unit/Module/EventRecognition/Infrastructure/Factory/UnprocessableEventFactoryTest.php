<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Application\Model\UnprocessableEvent;
use App\Module\EventRecognition\Infrastructure\Factory\UnprocessableEventFactory;
use PHPUnit\Framework\TestCase;

final class UnprocessableEventFactoryTest extends TestCase
{
    private const string DEFAULT_FAULT = 'Description field empty.';

    public function testItDoesNotSupportMessageWithDescription(): void
    {
        $service = new UnprocessableEventFactory();
        $model = new MessageModel(
            description: 'Description'
        );
        $this->assertFalse($service->supports($model));
    }

    public function testItSupportsMessageWithoutDescription(): void
    {
        $service = new UnprocessableEventFactory();
        $model = new MessageModel();
        $this->assertTrue($service->supports($model));
    }

    public function testItCreatesEvent(): void
    {
        $service = new UnprocessableEventFactory();
        $number = 123;
        $model = new MessageModel(
            number: $number
        );
        $result = $service->create($model);
        $this->assertEquals(
            new UnprocessableEvent($number, self::DEFAULT_FAULT),
            $result
        );
    }

    public function testItCreatesEventFromException(): void
    {
        $message = 'Some exception';
        $number = 456;
        $model = new MessageModel(
            number: $number
        );
        $exception = new \RuntimeException($message);
        $result = UnprocessableEventFactory::createFromException($model, $exception);
        $this->assertEquals(
            new UnprocessableEvent($number, $message),
            $result
        );
    }
}
