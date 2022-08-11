<?php

declare(strict_types=1);

namespace App\Tests\Api\Infrastructure\Transport\Serializer;

use App\Infrastructure\Transport\Serializer\MessageSerializer;

/**
 * Структура сообщения для тестирования @see MessageSerializer
 */
final class TestMessageForMessageSerializer
{
    public function __construct(
        public readonly string $stringProperty,
        public readonly int $intProperty
    ) {
    }
}
