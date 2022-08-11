<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport;

use App\Infrastructure\Transport\Connection\RabbitConnection;
use App\Infrastructure\Transport\Serializer\MessageSerializer;

/**
 * Диспетчер сообщений в очередь
 */
final class MessageDispatcher
{
    private RabbitConnection $transport;

    private MessageSerializer $serializer;

    public function __construct(RabbitConnection $transport)
    {
        $this->transport = $transport;
        $this->serializer = new MessageSerializer();
    }

    public function dispatch(string $receiverName, object $message, string $id = null): void
    {
        $this->transport->publish(
            $receiverName,
            $this->serializer->encode(RabbitMessage::create(message: $message, id: $id))
        );
    }
}
