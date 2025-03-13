<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Enum\EventPriorityEnum;
use App\Module\EventRecognition\Application\Enum\EventStatusEnum;
use App\Module\EventRecognition\Application\Enum\EventTypeEnum;
use App\Module\EventRecognition\Application\Exception\EventDescriptionMissingException;
use App\Module\EventRecognition\Application\Exception\EventNumberMissingException;
use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Application\Model\EmergencyEvent;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;
use App\Module\EventRecognition\Application\Resolver\EventPriorityResolverInterface;

use function str_contains;
use function strtolower;

final readonly class EmergencyEventFactory implements EventModelFactoryInterface
{
    private const string INSPECTION_TEXT = 'przegląd';

    public function __construct(
        private EventPriorityResolverInterface $priorityResolver
    ) {
    }

    public function supports(MessageModel $message): bool
    {
        $description = $message->getDescription();

        if (null === $description) {
            return false;
        }

        return !str_contains(strtolower($description), self::INSPECTION_TEXT);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function create(
        MessageModel $message,
        array $additionalData = []
    ): EventModelInterface {
        $number = $message->getNumber();
        $description = $message->getDescription();

        if (null === $number) {
            throw EventNumberMissingException::create();
        }

        if  (null === $description) {
            throw EventDescriptionMissingException::create();
        }

        $dueDate = $message->getDueDate();
        $date = null;
        $status = EventStatusEnum::NEW;

        if (null !== $dueDate) {
            $date = new \DateTime($dueDate);
            $status = EventStatusEnum::DATE;
        }

        return new EmergencyEvent(
            number: $number,
            description: $description,
            priority: $this->priorityResolver->resolve($description),
            date: $date,
            status: $status,
            comments: empty($additionalData['comments']) ? null : $additionalData['comments'],
            phoneNumber: $message->getPhone(),
        );
    }

    public static function fromArray(array $data): EventModelInterface
    {
        return new EmergencyEvent(
            number: $data['number'],
            description: $data['description'],
            priority: EventPriorityEnum::resolveByValue($data['priority'] ?? null),
            type: EventTypeEnum::resolveByValue($data['type'] ?? null),
            date: empty($data['date']) ? null : new \DateTime($data['date']),
            status: EventStatusEnum::resolveByValue($data['status'] ?? null),
            comments: empty($data['comments']) ? null :  $data['comments'],
            phoneNumber: empty($data['phoneNumber']) ? null : $data['phoneNumber'],
            createdAt: new \DateTime($data['createdAt']),
        );
    }
}
