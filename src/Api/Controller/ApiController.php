<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Infrastructure\Response\ResponseFactory;
use App\Bank\Command\SendRequestToBank;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ApiController
{
    private ResponseFactory $responseFactory;
    private MessageBusInterface $messageBus;

    public function __construct(ResponseFactory $responseFactory, MessageBusInterface $messageBus)
    {
        $this->responseFactory = $responseFactory;
        $this->messageBus = $messageBus;
    }

    /**
     * Отправка заявки в банк
     */
    #[Route(path: "request/create")]
    public function sendLoadRequestToBank(SendRequestToBank $message): Response
    {
        $this->messageBus->dispatch($message);

        return $this->responseFactory->ok();
    }
}
