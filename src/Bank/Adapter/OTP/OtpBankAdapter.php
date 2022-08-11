<?php

declare(strict_types=1);

namespace App\Bank\Adapter\OTP;

use App\Bank\Adapter\BankAdapter;

final class OtpBankAdapter implements BankAdapter
{
    public static function getCode(): string
    {
        return 'otp';
    }

    public static function getMetadataClass(): ?string
    {
        return OtpMetadata::class;
    }

    public function sendLoanRequest(string $url, string $blankId, ?object $metadata): void
    {
        /** @var OtpMetadata $metadata */
    }
}
