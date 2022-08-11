<?php

declare(strict_types=1);

namespace App\Tests\_fakes;

use App\Infrastructure\Transport\Connection\RabbitConnection;

final class FakeRabbitConnection implements RabbitConnection
{
    /**
     * @var array<string, array<string>>
     */
    private array $published = [];

    public function publish(string $receiverName, string $message): void
    {
        $this->published[$receiverName][] = $message;
    }

    public function getFirstMessageForReceiver(string $receiverName): ?string
    {
        if (\array_key_exists($receiverName, $this->published) !== true) {
            return null;
        }

        return $this->published[$receiverName][0] ?? null;
    }

    public function declareQueues(array $queueNames): void
    {
    }
}
