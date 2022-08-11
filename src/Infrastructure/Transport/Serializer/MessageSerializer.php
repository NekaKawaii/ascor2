<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport\Serializer;

use App\Infrastructure\Transport\MessageMetadata;
use App\Infrastructure\Transport\RabbitMessage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Сериалайзер для сообщений в очередь
 */
final class MessageSerializer
{
    private Serializer $serializer;

    public function __construct(Serializer $serializer = null)
    {
        $this->serializer = $serializer ?? self::createSerializer();
    }

    /**
     * Сериализация сообщения в JSON-строку для отправки в очередь
     */
    public function encode(RabbitMessage $message): string
    {
        /** @var array{message: array<string,mixed>} $normalized */
        $normalized = $this->serializer->normalize($message);
        $normalized['message']['__type'] = \get_class($message->message);

        return $this->serializer->encode($normalized, 'json');
    }

    /**
     * Десериализация JSON-строки из сообщения для восстановления объекта-сообщения из очереди
     */
    public function decode(string $jsonString): RabbitMessage
    {
        /** @var array{message: array{__type: class-string}, meta: array<string,mixed>} $decoded */
        $decoded = $this->serializer->decode($jsonString, 'json');

        /** @var object $innerMessage */
        $innerMessage = $this->serializer->denormalize($decoded['message'], $decoded['message']['__type']);

        /** @var MessageMetadata $meta */
        $meta = $this->serializer->denormalize($decoded['meta'], MessageMetadata::class);

        return new RabbitMessage(meta: $meta, message: $innerMessage);
    }

    /**
     * Создание инстанса сериалайзера для проведения сериализации
     */
    public static function createSerializer(): Serializer
    {
        return new Serializer(
            [
                new ArrayDenormalizer(),
                new PropertyNormalizer(),
                new DateTimeNormalizer([
                    DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s.uO',
                    DateTimeNormalizer::TIMEZONE_KEY => new \DateTimeZone('GMT')
                ]),
            ],
            [
                new JsonEncoder()
            ]
        );
    }
}
