<?php

namespace App\Infrastructure\Transport\Connection;

interface RabbitConnection
{
    public function publish(string $receiverName, string $message): void;

    /**
     * @param array<string> $queueNames
     */
    public function declareQueues(array $queueNames): void;
}
