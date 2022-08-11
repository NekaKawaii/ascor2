<?php

declare(strict_types=1);

namespace App\Bank\Command\Struct;

final class CallbackData
{
    /**
     * @param string $url URL, по которому необходимо вернуть информацию по заявке
     * @param array $returnData Дополнительные данные, которые необходимо передать с возвращаемой информацией
     */
    public function __construct(
        public readonly string $url,
        public readonly array $returnData = []
    ) {
    }
}
