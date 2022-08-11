<?php

declare(strict_types=1);

namespace App\Bank\Command;

use App\Bank\Command\Struct\BankLoanRequestData;
use App\Bank\Command\Struct\CallbackData;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Отправить заявку в банк
 */
final class SendRequestToBank
{
    /**
     * @param ?string $messageId Id сообщения для трейсинга в формате UUID
     * @param string $bank Строковый код банка
     * @param int $blankId Id бланка в системе POS
     */
    public function __construct(
        #[Assert\Uuid]
        public readonly ?string $messageId,
        #[Assert\NotBlank]
        public readonly string $bank,
        #[Assert\GreaterThan(0)]
        public readonly int $blankId,
        #[Assert\Valid]
        public readonly BankLoanRequestData $request,
        #[Assert\Valid]
        public readonly CallbackData $callback
    ) {
    }
}
