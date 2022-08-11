<?php

declare(strict_types=1);

namespace App\Bank\Adapter\OTP;

use Symfony\Component\Validator\Constraints as Assert;

final class OtpMetadata
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $action
    ) {
    }
}
