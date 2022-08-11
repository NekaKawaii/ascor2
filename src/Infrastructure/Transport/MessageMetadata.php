<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport;

use Symfony\Component\Uid\Uuid;

use function App\now;

final class MessageMetadata
{
    public function __construct(
        public readonly string $id,
        public readonly \DateTimeImmutable $occurredAt
    ) {
    }

    public static function create(string $id = null): self
    {
        return new self($id ?? Uuid::v4()->toRfc4122(), now());
    }
}
