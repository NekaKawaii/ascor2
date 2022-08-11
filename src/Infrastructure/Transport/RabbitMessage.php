<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport;

/**
 * Сообщение в очередь
 */
final class RabbitMessage
{
    /**
     * @param MessageMetadata $meta
     * @param object $message Сообщение
     */
    public function __construct(
        public readonly MessageMetadata $meta,
        public readonly object $message,
    ) {
    }

    public static function create(object $message, string $id = null): self
    {
        return new self(meta: MessageMetadata::create($id), message: $message);
    }
}
