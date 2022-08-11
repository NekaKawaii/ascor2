<?php

declare(strict_types=1);

namespace App\Tests\Api\Infrastructure\Transport\Serializer;

use App\Infrastructure\Transport\MessageMetadata;
use App\Infrastructure\Transport\RabbitMessage;
use App\Infrastructure\Transport\Serializer\MessageSerializer;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class MessageSerializerTest extends TestCase
{
    use MatchesSnapshots;

    private MessageSerializer $serializer;

    /**
     * Сериалайзер переводит объект сообщения в правильный формат JSON
     */
    public function testItSerializeMessageToProperString(): void
    {
        $message = new RabbitMessage(
            meta: new MessageMetadata(id: "00000000-0000-0000-0000-000000000000", occurredAt: new \DateTimeImmutable('2020-01-01 10:20:30')),
            message: new TestMessageForMessageSerializer(stringProperty: 'testString', intProperty: 5)
        );

        $decoded = $this->serializer->encode($message);

        $this->assertMatchesJsonSnapshot($decoded);
    }

    /**
     * Сериалайзер восстанавливает правильный объект сообщения из JSON строки
     */
    public function testItDeserializeMessageFromProperString(): void
    {
        $expectedMessage = new RabbitMessage(
            meta: new MessageMetadata(id: "00000000-0000-0000-0000-000000000000", occurredAt: new \DateTimeImmutable('2020-01-01 10:20:30')),
            message: new TestMessageForMessageSerializer(stringProperty: 'testString', intProperty: 5)
        );

        $jsonString = '{"message": {"__type": "App\\\\Tests\\\\Api\\\\Infrastructure\\\\Transport\\\\Serializer\\\\TestMessageForMessageSerializer", "stringProperty": "testString", "intProperty": 5}, "meta": {"id": "00000000-0000-0000-0000-000000000000", "occurredAt": "2020-01-01 10:20:30.000000+0000"}}';

        $message = $this->serializer->decode($jsonString);

        self::assertEquals($expectedMessage, $message);
    }

    protected function setUp(): void
    {
        $this->serializer = new MessageSerializer();
    }
}
