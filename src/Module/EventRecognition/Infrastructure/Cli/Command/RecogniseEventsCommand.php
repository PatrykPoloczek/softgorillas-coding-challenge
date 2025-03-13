<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Cli\Command;

use App\Module\EventRecognition\Application\Model\ProcessSummary;
use App\Module\EventRecognition\Application\Model\ProcessSummaryEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Module\EventRecognition\Application\Handler\RecogniseEventsHandlerInterface;

use function strtolower;
use function sprintf;
use function ucfirst;
use function count;

#[AsCommand(
    name: 'events:recognise',
    description: 'Recognise the event types from the input file and categorise them.'
)]
final class RecogniseEventsCommand extends Command
{
    public const string INPUT_FILE_ARGUMENT = 'inputFile';
    public const string OUTPUT_TYPE_ARGUMENT = 'outputType';

    public function __construct(
        private readonly RecogniseEventsHandlerInterface $handler,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDefinition(
                new InputDefinition([
                    new InputArgument(
                        name: self::INPUT_FILE_ARGUMENT,
                        mode: InputArgument::REQUIRED,
                        description: 'Input file absolute path.'
                    ),
                    new InputArgument(
                        name: self::OUTPUT_TYPE_ARGUMENT,
                        mode: InputArgument::OPTIONAL,
                        description: 'Output file type.',
                        default: 'json'
                    )
                ])
            )
        ;
    }

    public function run(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->logger->info('Events recognition process is about to start.');

        try {
            list($inputFile, $outputType) = $this->retrieveArguments($input);

            $result = $this->handler->handle($inputFile, strtolower($outputType));
            $this->displayResult($result, $output);
            $this->logger->info('Process successfully completed.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->logger->error(
                'Failed to process the request.',
                [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'parentMessage' => $exception->getPrevious()?->getMessage(),
                ]
            );

            return self::FAILURE;
        }
    }

    private function displayResult(
        ProcessSummary $result,
        OutputInterface $output
    ): void {
        $summary = $result->getSummary();
        $output->writeln('Process finished.');

        foreach ($summary as $type => $entries) {
            $count = is_array($entries)
                ? count($entries)
                : $entries
            ;

            $output->writeln(
                sprintf(
                    '%s processed %d:',
                    ucfirst($type),
                    $count
                )
            );

            if (!is_array($entries)) {
                continue;
            }

            /** @var ProcessSummaryEntry $entry */
            foreach ($entries as $entry) {
                $output->writeln(
                    sprintf(
                        'Identifier: %d - Message: %s',
                        $entry->getNumber(),
                        $entry->getMessage()
                    )
                );
            }
        }
    }

    private function retrieveArguments(InputInterface $input): array
    {
        if ($input instanceof ArrayInput) {
            return [
                $input->getParameterOption(self::INPUT_FILE_ARGUMENT),
                $input->getParameterOption(self::OUTPUT_TYPE_ARGUMENT)
            ];
        }

        return [
            $input->getArgument(self::INPUT_FILE_ARGUMENT),
            $input->getArgument(self::OUTPUT_TYPE_ARGUMENT),
        ];
    }
}
