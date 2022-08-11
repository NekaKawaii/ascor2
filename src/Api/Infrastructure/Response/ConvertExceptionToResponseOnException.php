<?php

declare(strict_types=1);

namespace App\Api\Infrastructure\Response;

use App\Common\Exception\ApplicationException;
use App\Common\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/**
 * Конвертирует исключения в ошибку API
 */
final class ConvertExceptionToResponseOnException implements EventSubscriberInterface
{
    private ResponseFactory $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Формирует ответ исходя из вида исключения и его параметров
     */
    private function resolveResponse(\Throwable $t): JsonResponse
    {
        // Ошибка валидации
        if ($t instanceof ValidationException) {
            return $this->responseFactory->requestError(
                code: $t->getCode(),
                message: $t->getMessage(),
                data: $t->violations
            );
        }

        // Все исключения уровня приложения
        if ($t instanceof ApplicationException) {
            return $this->responseFactory->requestError(code: $t->getCode(), message: $t->getMessage());
        }

        // Все исключения, оставшиеся необработанными
        return $this->responseFactory->internalServerError();
    }

    /**
     * Установка ответа, который должен вернуться пользователю исходя из возникшего исключения
     */
    public function setResponseOnException(ExceptionEvent $event): void
    {
        $t = $event->getThrowable();

        // Распаковываем истинное исключение из исключения обработчика
        if ($t instanceof HandlerFailedException) {
            $t = $t->getPrevious();
        }

        $event->setResponse($this->resolveResponse($t));
    }

    public static function getSubscribedEvents(): iterable
    {
        return [
            KernelEvents::EXCEPTION => 'setResponseOnException'
        ];
    }
}
