<?php

declare(strict_types=1);

namespace App\Module\EventRecognition\Infrastructure\Factory;

use App\Module\EventRecognition\Application\Model\MessageModel;

readonly class MessageModelFactory
{
    private const string NUMBER_FIELD = 'number';
    private const string DESCRIPTION_FIELD = 'description';
    private const string DUE_DATE_FIELD = 'dueDate';
    private const string PHONE_FIELD = 'phone';

    /**
     * @param array<string, int|string> $data
     *
     * @return MessageModel
     */
    public function create(array $data): MessageModel
    {
        return new MessageModel(
            empty($data[self::NUMBER_FIELD]) ? null :  $data[self::NUMBER_FIELD],
            empty($data[self::DESCRIPTION_FIELD]) ? null :  $data[self::DESCRIPTION_FIELD],
            empty($data[self::DUE_DATE_FIELD]) ? null :  $data[self::DUE_DATE_FIELD],
            empty($data[self::PHONE_FIELD]) ? null :  $data[self::PHONE_FIELD]
        );
    }
}