<?php

declare(strict_types=1);

namespace App\Bank\Command\Struct;

final class BankLoanRequestData
{
    /**
     * @param string $url URL, по которому необходимо выполнить запрос для передачи заявки на кредит в банк
     * @param string $body Тело запроса, которое должно быть передано в банк
     * @param array $metadata Дополнительные данные для определенного хендлера, необходимые для формирования правильного запроса
     */
    public function __construct(
        public readonly string $url,
        public readonly string $body,
        public readonly array $metadata = []
    ) {
    }
}
