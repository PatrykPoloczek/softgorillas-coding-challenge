<?php

declare(strict_types=1);

namespace App\Tests\Units\Module\EventRecognition\Infrastructure\Locator;

use App\Module\EventRecognition\Application\Enum\OutputTypeEnum;
use App\Module\EventRecognition\Application\Exception\WriterNotFoundException;
use App\Module\EventRecognition\Application\Writer\WriterInterface;
use App\Module\EventRecognition\Infrastructure\Locator\WriterLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class WriterLocatorTest extends TestCase
{
    public function testItReturnsWriter(): void
    {
        $writer = $this->createMock(WriterInterface::class);
        $container = $this->createMock(ContainerBuilder::class);
        $outputTypeEnum = OutputTypeEnum::JSON;
        $container
            ->expects(self::once())
            ->method('has')
            ->with($outputTypeEnum->value)
            ->willReturn(true)
        ;
        $container
            ->expects(self::once())
            ->method('get')
            ->with($outputTypeEnum->value)
            ->willReturn($writer)
        ;
        $service = new WriterLocator($container);

        $this->assertEquals(
            $writer,
            $service->get($outputTypeEnum)
        );
    }

    public function testItThrowsException(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $outputTypeEnum = OutputTypeEnum::XML;
        $container
            ->expects(self::once())
            ->method('has')
            ->with($outputTypeEnum->value)
            ->willReturn(false)
        ;

        $service = new WriterLocator($container);

        $this->expectException(WriterNotFoundException::class);
        $service->get($outputTypeEnum);
    }
}
