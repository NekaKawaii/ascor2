<?php

declare(strict_types=1);

namespace App\Bank\CommandHandler;

use App\Bank\BankAdapters;
use App\Bank\Command\SendRequestToBank;
use App\Bank\Exception\MalformedRequestMetadataException;
use App\Bank\Exception\UnsupportedBankCode;
use App\Common\Exception\ValidationException;
use App\Infrastructure\Transport\MessageDispatcher;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function App\uuidv4;

/**
 * Кладем запрос на отправку заявки в банк в очередь
 */
final class PutRequestToQueueOnSendRequestToBank implements MessageHandlerInterface
{
    private MessageDispatcher $dispatcher;

    private BankAdapters $banks;

    private ValidatorInterface $validator;

    private DenormalizerInterface $denormalizer;

    public function __construct(
        MessageDispatcher $dispatcher,
        BankAdapters $banks,
        DenormalizerInterface $denormalizer,
        ValidatorInterface $validator
    ) {
        $this->dispatcher = $dispatcher;
        $this->banks = $banks;
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    public function __invoke(SendRequestToBank $command): void
    {
        // Проверяем, поддерживает ли система этот банк
        if ($this->banks->support($command->bank) !== true) {
            throw new UnsupportedBankCode();
        }

        // Валидируем метадату запроса
        $metadataClass = $this->banks->getMetadataClass($command->bank);

        if ($metadataClass !== null && \class_exists($metadataClass)) {
            $this->validateMetadata($metadataClass, $command->request->metadata);
        }

        // Отправляем сообщение в очередь
        $this->dispatcher->dispatch(
            receiverName: $command->bank,
            message: $command,
            id: $command->messageId ?? uuidv4()
        );
    }

    private function validateMetadata(string $metadataClass, array $metadata): void
    {
        try {
            /** @var object $metadata */
            $metadata = $this->denormalizer->denormalize($metadata, $metadataClass);
        } catch (MissingConstructorArgumentsException $e) {
            throw new MalformedRequestMetadataException(previous: $e);
        }

        $errors = $this->validator->validate($metadata);

        if ($errors->count() > 0) {
            throw new ValidationException('Bank metadata invalid', $errors);
        }
    }
}
