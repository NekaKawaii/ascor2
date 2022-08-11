<?php

declare(strict_types=1);

namespace App\Api\Exception;

use App\Common\Exception\ApplicationException;

/**
 * Нарушена структура запроса
 */
final class RequestMalformedException extends ApplicationException
{
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Malformed request body', 421, $previous);
    }
}
