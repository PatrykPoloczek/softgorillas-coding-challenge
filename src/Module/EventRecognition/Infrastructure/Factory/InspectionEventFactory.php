<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Enum\EventStatusEnum;
use App\Module\EventRecognition\Application\Enum\EventTypeEnum;
use App\Module\EventRecognition\Application\Exception\EventDescriptionMissingException;
use App\Module\EventRecognition\Application\Exception\EventNumberMissingException;
use App\Module\EventRecognition\Application\Model\InspectionEvent;
use App\Module\EventRecognition\Application\Model\EventModelInterface;
use App\Module\EventRecognition\Application\Factory\EventModelFactoryInterface;
use App\Module\EventRecognition\Application\Model\MessageModel;

use function str_contains;
use function strtolower;

final readonly class InspectionEventFactory implements EventModelFactoryInterface
{
    private const string INSPECTION_TEXT = 'przegląd';

    public function supports(MessageModel $message): bool
    {
        $description = $message->getDescription();

        if (null === $description) {
            return false;
        }

        return str_contains(strtolower($description), self::INSPECTION_TEXT);
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
        $weekNumber = null;
        $status = EventStatusEnum::NEW;

        if (null !== $dueDate) {
            $date = new \DateTime($dueDate);
            $weekNumber = (int) $date->format('W');
            $status = EventStatusEnum::PLANNED;
        }

        return new InspectionEvent(
            number: $number,
            description: $description,
            date: $date,
            weekNumber: $weekNumber,
            status: $status,
            recommendations: empty($additionalData['recommendations']) ? null : $additionalData['recommendations'],
            phoneNumber: $message->getPhone()
        );
    }

    public static function fromArray(array $data): EventModelInterface
    {
        return new InspectionEvent(
            number: $data['number'],
            description: $data['description'],
            type: EventTypeEnum::resolveByValue($data['type'] ?? null),
            date: empty($data['date']) ? null : new \DateTime($data['date']),
            weekNumber: empty($data['weekOfYear']) ? null : $data['weekOfYear'],
            status: EventStatusEnum::resolveByValue($data['status'] ?? null),
            recommendations: empty($data['recommendations']) ? null :  $data['recommendations'],
            phoneNumber: empty($data['phoneNumber']) ? null : $data['phoneNumber'],
            createdAt: new \DateTime($data['createdAt']),
        );
    }
}
