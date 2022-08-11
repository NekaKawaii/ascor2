<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Response;

use App\Api\Infrastructure\Request\ApiRequestSerializerFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Фабрика ответов для Api
 */
final class ResponseFactory
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Ответ 200 OK без тела
     */
    public function ok(): Response
    {
        return new Response();
    }

    /**
     * Ошибочный запрос, структура с кодом, сообщением и дополнительными данными (если есть)
     */
    public function requestError(int $code, string $message, array $data = []): JsonResponse
    {
        $response = ['code' => $code, 'message' => $message];

        if (empty($data) === false) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, 422);
    }

    /**
     * Ошибка на сервере
     */
    public function internalServerError(): JsonResponse
    {
        return new JsonResponse(['message' => 'Internal Server Error'], 500);
    }

    /**
     * Ответ в виде структуры ответа от API
     */
    public function response(ApiResponse $response): JsonResponse
    {
        return new JsonResponse(data: $this->serializer->serialize($response, 'json'), json: true);
    }
}
