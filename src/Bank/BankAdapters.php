<?php

declare(strict_types=1);

namespace App\Bank;

use App\Bank\Adapter\BankAdapter;
use App\Bank\Adapter\OTP\OtpBankAdapter;
use App\Infrastructure\Transport\MessageDispatcher;

final class BankAdapters
{
    /**
     * Адаптеры банков
     *
     * @var array<string, BankAdapter>
     */
    private array $adapters = [];

    public function __construct(MessageDispatcher $dispatcher)
    {
        $this->adapters[OtpBankAdapter::getCode()] = new OtpBankAdapter();

        // TODO: Вынести отсюда
        $dispatcher->declareQueues($this->getAllCodes());
    }

    public function support(string $bankCode): bool
    {
        return \array_key_exists($bankCode, $this->adapters);
    }

    public function getMetadataClass(string $bankCode): ?string
    {
        return $this->adapters[$bankCode]->getMetadataClass();
    }

    /**
     * @return array<string>
     */
    public function getAllCodes(): array
    {
        return \array_keys($this->adapters);
    }
}
