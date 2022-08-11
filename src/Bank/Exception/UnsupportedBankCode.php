<?php

declare(strict_types=1);

namespace App\Bank\Exception;

use App\Common\Exception\ApplicationException;

/**
 * Код банка не поддерживается системой
 */
final class UnsupportedBankCode extends ApplicationException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Unsupported bank code given', 100, $previous);
    }
}
