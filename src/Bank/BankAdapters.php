<?php

declare(strict_types=1);

namespace App\Bank;

use App\Bank\Adapter\BankAdapter;
use App\Bank\Adapter\OTP\OtpBankAdapter;

final class BankAdapters
{
    /**
     * Адаптеры банков
     *
     * @var array<string, BankAdapter>
     */
    private array $adapters = [];

    public function __construct()
    {
        $this->adapters[OtpBankAdapter::getCode()] = new OtpBankAdapter();
    }

    public function support(string $bankCode): bool
    {
        return \array_key_exists($bankCode, $this->adapters);
    }

    public function getMetadataClass(string $bankCode): ?string
    {
        return $this->adapters[$bankCode]->getMetadataClass();
    }

    public function getAllCodes(): array
    {
        return \array_keys($this->adapters);
    }
}
