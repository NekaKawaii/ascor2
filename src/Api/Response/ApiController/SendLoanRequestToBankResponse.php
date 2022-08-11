<?php

declare(strict_types=1);

namespace App\Api\Response\ApiController;

use App\Api\Infrastructure\Response\ApiResponse;

/**
 * Ответ ендпоинта отправки запроса в банк
 */
final class SendLoanRequestToBankResponse implements ApiResponse
{
    /**
     * @param string $id Id сообщения, которое было положено в очередь
     */
    public function __construct(
        public readonly string $id
    ){
    }
}
