<?php

namespace App\Bank\Adapter;

/**
 * Адаптер банка
 */
interface BankAdapter
{
    /**
     * Строковое название банка
     */
    public static function getCode(): string;

    /**
     * Возвращает класс метаданных, который необходим для отправки в банк
     *
     * @return ?class-string
     */
    public static function getMetadataClass(): ?string;

    /**
     * Отправка заявки на кредит в банк
     */
    public function sendLoanRequest(string $url, string $blankId, ?object $metadata): void;
}
