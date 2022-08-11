<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport\Connection;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Коннект к кролику с помощью php-amqplib
 */
final class PhpAmqpLibConnection implements RabbitConnection
{
    private AMQPStreamConnection $connection;

    private AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function publish(string $receiverName, string $message): void
    {
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, '', $receiverName);
        $this->closeConnection();
    }

    public function declareQueues(array $queueNames): void
    {
        foreach ($queueNames as $queueName) {
            $this->channel->queue_declare($queueName, false, true, false, false);
        }
    }

    private function closeConnection(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
