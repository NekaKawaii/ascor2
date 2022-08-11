<?php

declare(strict_types=1);

namespace App\Common\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Исключение непройденой валидации
 */
final class ValidationException extends ApplicationException
{
    /**
     * Массив ошибок с путями до пропертей валидируемого объекта
     *
     * @var array<string, array<string>>
     */
    public array $violations = [];

    public function __construct(string $message, ConstraintViolationListInterface $violations, ?\Throwable $previous = null)
    {
        parent::__construct($message, 200, $previous);

        // Конвертируем ошибки в массив ошибок
        foreach ($violations as $violation) {
            $this->violations[$violation->getPropertyPath()][] = (string)$violation->getMessage();
        }
    }
}
