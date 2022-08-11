<?php

namespace App;

use Symfony\Component\Uid\Uuid;

/**
 * Возвращает DateTimeImmutable с текущим моментом времени и микросекундами.
 *
 * @psalm-pure
 */
function now(): \DateTimeImmutable
{
    /**
     * @var \DateTimeImmutable $now
     * @psalm-suppress ImpureFunctionCall
     */
    $now = \DateTimeImmutable::createFromFormat('0.u00 U', microtime());

    return $now;
}

/**
 * Генерация UUID v4 в строковом виде
 */
function uuidv4(): string
{
    return Uuid::v4()->toRfc4122();
}
