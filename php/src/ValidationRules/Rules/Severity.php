<?php

namespace  App\Php\ValidationRules\Rules;

/**
 * Used for define severity level into ValidationRules
 */
enum Severity: int
{
    case INFO = 0;
    case WARNING = 1;
    case ERROR = 2;
    case FATAL = 3;
}
