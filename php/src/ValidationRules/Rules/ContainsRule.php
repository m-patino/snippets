<?php

namespace App\Php\ValidationRules\Rules;

use App\Php\ValidationRules\Rules\Rule;

class ContainsRule implements Rule
{
    /**
     * @param $value
     * @param string $errorMessage
     * @param Severity $severity
     */
    public function __construct(private $value, private readonly string $errorMessage, private readonly Severity $severity)
    {
    }

    /**
     * @param $valueToCheck
     * @return bool
     */
    public function check($valueToCheck): bool
    {
        return str_contains($valueToCheck, $this->value);
    }

    /**
     * @return int
     */
    public function getSeverity(): int
    {
        return $this->severity->value;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
