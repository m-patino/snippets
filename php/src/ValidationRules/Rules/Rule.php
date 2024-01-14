<?php

namespace  App\Php\ValidationRules\Rules;

interface Rule
{
    /**
     * @param $value
     * @param string $errorMessage
     * @param Severity $severity
     */
    public function __construct($value,string $errorMessage, Severity $severity);

    /**
     * @param $valueToCheck
     * @return bool
     */
    public function check($valueToCheck):bool;

    /**
     * @return int
     */
    public function getSeverity():int;

    /**
     * @return string
     */
    public function getErrorMessage():string;
}

