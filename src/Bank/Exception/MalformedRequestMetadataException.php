<?php

declare(strict_types=1);

namespace App\Bank\Exception;

use App\Common\Exception\ApplicationException;

final class MalformedRequestMetadataException extends ApplicationException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('Malformed bank request metadata', 101, $previous);
    }
}
