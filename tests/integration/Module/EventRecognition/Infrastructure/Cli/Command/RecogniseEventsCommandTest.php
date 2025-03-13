<?php

declare(strict_types=1);

namespace App\Tests\Integration\Module\EventRecognition\Infrastructure\Cli\Command;

use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Infrastructure\Cli\Command\RecogniseEventsCommand;
use App\Module\EventRecognition\Infrastructure\Factory\EmergencyEventFactory;
use App\Module\EventRecognition\Infrastructure\Factory\InspectionEventFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RecogniseEventsCommandTest extends KernelTestCase
{
    private const string COMMAND_NAME = 'events:recognise';
    private const string OUTPUT_STORAGE = 'var/output';
    private const string INPUT_STORAGE = 'tests/resources/payloads/input';
    private const string EXPECTED_OUTPUT_STORAGE = 'tests/resources/payloads/output';
    private const string DATE_FORMAT = 'Y-m-d';
    private const string EMERGENCY_OUTPUT_NAME = 'zgloszenia_awarii';
    private const string INSPECTIONS_OUTPUT_NAME = 'przeglady';

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->cleanStorage();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cleanStorage();
    }

    public function testUnsupportedOutputType(): void
    {
        $application = new Application(self::$kernel);
        $command = $application->find(self::COMMAND_NAME);
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute([
            RecogniseEventsCommand::INPUT_FILE_ARGUMENT => $this->getContainer()->getParameter('kernel.project_dir'),
            RecogniseEventsCommand::OUTPUT_TYPE_ARGUMENT => 'csv',
        ]);

        $this->assertEquals(
            RecogniseEventsCommand::FAILURE,
            $result
        );
    }

    public function testInvalidInputFile(): void
    {
        $application = new Application(self::$kernel);
        $command = $application->find(self::COMMAND_NAME);
        $commandTester = new CommandTester($command);
        $result = $commandTester->execute([
            RecogniseEventsCommand::INPUT_FILE_ARGUMENT => $this->getContainer()->getParameter('kernel.project_dir'),
            RecogniseEventsCommand::OUTPUT_TYPE_ARGUMENT => 'json',
        ]);

        $this->assertEquals(
            RecogniseEventsCommand::FAILURE,
            $result
        );
    }

    public function testFullSuccess(): void
    {
        $application = new Application(self::$kernel);
        $command = $application->find(self::COMMAND_NAME);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            RecogniseEventsCommand::INPUT_FILE_ARGUMENT => $this->prepareFullPath('valid-input.json'),
            RecogniseEventsCommand::OUTPUT_TYPE_ARGUMENT => 'json',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $this->verifyEmergencyEventOutput(
            $this->prepareFullPath(
                'emergencies_1.json',
                self::EXPECTED_OUTPUT_STORAGE
            ),
            [
                '{{created_at}}' => (new \DateTime())->format(self::DATE_FORMAT)
            ]
        );
        $this->verifyInspectionEventOutput();

        $this->verifyConsoleDisplay(
            $commandTester->getDisplay(),
            [
                'Process finished.',
                'Total processed 1:',
                'Emergencies processed 1:',
                'Identifier: 1 - Message: Successfully processed.',
                '',
            ]
        );
    }

    public function testSuccessWithVariousEvents(): void
    {
        $application = new Application(self::$kernel);
        $command = $application->find(self::COMMAND_NAME);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            RecogniseEventsCommand::INPUT_FILE_ARGUMENT => $this->prepareFullPath('valid-input-1.json'),
            RecogniseEventsCommand::OUTPUT_TYPE_ARGUMENT => 'json',
        ]);

        $commandTester->assertCommandIsSuccessful();

        $this->verifyEmergencyEventOutput(
            $this->prepareFullPath(
                'emergencies_1.json',
                self::EXPECTED_OUTPUT_STORAGE
            ),
            [
                '{{created_at}}' => (new \DateTime())->format(self::DATE_FORMAT)
            ]
        );
        $this->verifyInspectionEventOutput(
            $this->prepareFullPath(
                'inspections_1.json',
                self::EXPECTED_OUTPUT_STORAGE
            ),
            [
                '{{created_at}}' => (new \DateTime())->format(self::DATE_FORMAT)
            ]
        );

        $this->verifyConsoleDisplay(
            $commandTester->getDisplay(),
            [
                'Process finished.',
                'Total processed 4:',
                'Emergencies processed 1:',
                'Identifier: 1 - Message: Successfully processed.',
                'Unprocessable processed 1:',
                'Identifier: 3 - Message: Description field empty.',
                'Inspections processed 1:',
                'Identifier: 4 - Message: Successfully processed.',
                '',
            ]
        );
    }

    private function cleanStorage(): void
    {
        $storagePath = sprintf(
            '%s/var/output',
            $this->getContainer()->getParameter('kernel.project_dir')
        );

        foreach (scandir($storagePath) as $file) {
            $path = sprintf('%s/%s', $storagePath, $file);

            if (is_dir($path)) {
                continue;
            }

            unlink($path);
        }
    }

    private function prepareFullPath(
        string $file,
        string $storage = self::INPUT_STORAGE
    ): string {
        return sprintf(
            '%s/%s/%s',
            $this->getContainer()->getParameter('kernel.project_dir'),
            $storage,
            $file
        );
    }

    /**
     * @param string $actualResult
     * @param array<int, string> $expectedResult
     * @return void
     */
    private function verifyConsoleDisplay(
        string $actualResult,
        array $expectedResult = []
    ): void {
        $formattedResult = explode("\r\n", $actualResult);
        $this->assertEquals($expectedResult, $formattedResult);
    }

    private function findOutput(string $name): ?string
    {
        $storagePath = sprintf(
            '%s/%s',
            $this->getContainer()->getParameter('kernel.project_dir'),
            self::OUTPUT_STORAGE
        );

        foreach (scandir($storagePath) as $file) {
            $path = sprintf('%s/%s', $storagePath, $file);

            if (is_dir($path)) {
                continue;
            }

            if (!str_contains($path, $name)) {
                continue;
            }

            return file_get_contents($path);
        }

        return null;
    }

    private function verifyEmergencyEventOutput(
        ?string $expectedOutput = null,
        array $replace = []
    ): void {
        $output = $this->getOutput($expectedOutput, $replace);
        $expectedOutput = null === $output
            ? null
            : array_map(
                fn (array $entry): EventModelInterface => EmergencyEventFactory::fromArray($entry),
                $output
            )
        ;

        $actualOutput = $this->findOutput(sprintf('%s.json', self::EMERGENCY_OUTPUT_NAME));

        if (null !== $actualOutput) {
            $actualOutput = json_decode($actualOutput, true);
            $actualOutput = array_map(
                fn (array $entry): EventModelInterface => EmergencyEventFactory::fromArray($entry),
                $actualOutput
            );
        }

        $this->assertEquals(
            $expectedOutput,
            $actualOutput
        );
    }

    private function verifyInspectionEventOutput(
        ?string $expectedOutput = null,
        array $replace = []
    ): void {
        $output = $this->getOutput($expectedOutput, $replace);
        $expectedOutput = null === $output
            ? null
            : array_map(
                fn (array $entry): EventModelInterface => InspectionEventFactory::fromArray($entry),
                $output
            )
        ;

        $actualOutput = $this->findOutput(sprintf('%s.json', self::INSPECTIONS_OUTPUT_NAME));

        if (null !== $actualOutput) {
            $actualOutput = json_decode($actualOutput, true);
            $actualOutput = array_map(
                fn (array $entry): EventModelInterface => InspectionEventFactory::fromArray($entry),
                $actualOutput
            );
        }

        $this->assertEquals(
            $expectedOutput,
            $actualOutput
        );
    }

    private function getOutput(
        ?string $filename,
        array $replace = []
    ): ?array {
        if (null === $filename) {
            return null;
        }

        $content = file_get_contents($filename);
        $content = str_replace(
            array_keys($replace),
            array_values($replace),
            $content
        );

        return json_decode($content, true);
    }
}
