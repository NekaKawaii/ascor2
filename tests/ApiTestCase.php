<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Transport\Connection\RabbitConnection;
use App\Kernel;
use App\Tests\_fakes\FakeRabbitConnection;
use App\Tests\_tools\JsonApiRequest;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class ApiTestCase extends WebTestCase
{
    use MatchesSnapshots;

    private KernelBrowser $client;

    /**
     * Отлавливать и показывать исключения, которые произошли в процессе обработки запроса
     */
    private bool $catchExceptions = false;

    /**
     * Запрос к API в тестовом окружении
     */
    public function requestJsonApi(JsonApiRequest $request): array
    {
        // Удаляем перехватчиков exception из диспетчера если надо
        if ($this->catchExceptions) {
            $dispatcher = $this->fetchFromContainer(EventDispatcherInterface::class);

            /** @var callable $listener */
            foreach ($dispatcher->getListeners('kernel.exception') as $listener) {
                $dispatcher->removeListener('kernel.exception', $listener);
            }
        }

        $rawResponse = $this->doRequest($request);
        $statusCode = $rawResponse->getStatusCode();
        $response = (string)$rawResponse->getContent();

        self::assertSame($request->expectedStatusCode, $statusCode, 'Unexpected status code. Body: ' . $response);

        if ($response === '') {
            return [];
        }

        $decoded = \json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (\is_array($decoded) !== true) {
            throw new \Exception('Unexpected response from Json API: ' . $decoded);
        }

        return $decoded;
    }

    /**
     * Включить отлавливание исключений тестами
     */
    protected function catchExceptions(): void
    {
        $this->catchExceptions = true;
    }

    /**
     * @template S
     *
     * @param class-string<S> $class
     *
     * @return S
     */
    protected function fetchFromContainer(string $class): object
    {
        /** @var S $service */
        $service = $this->getContainer()->get($class);

        return $service;
    }

    protected function rabbitConnection(): FakeRabbitConnection
    {
        /** @var FakeRabbitConnection $transport */
        $transport = $this->fetchFromContainer(RabbitConnection::class);

        return $transport;
    }

    /**
     * Застывшие часы для приложения
     *
     * Работают только для функции @see \App\now() или microtime()
     * Создание любых DateTime* мокаться застывшим временем не будут
     */
    protected function mockClockOn(\DateTimeInterface $time): void
    {
        ClockMock::withClockMock($time->getTimestamp());
    }

    /**
     * Запрос в HTTP API
     */
    private function doRequest(JsonApiRequest $request): Response
    {
        // Делаем запрос
        $this->client->request(
            $request->method,
            $request->uri,
            $request->parameters,
            [], // files
            $request->headers,
            $request->jsonBody !== [] ? json_encode($request->jsonBody) : null,
            false // change history
        );

        return $this->client->getResponse();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        // Регаем мокинг часов
        ClockMock::register(Kernel::class); // функция now находится в App неймспейсе
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
