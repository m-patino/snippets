<?php

interface RuleInterface 
{    
    const SEVERITY_INFO = 0;
    const SEVERITY_WARNING = 1;
    const SEVERITY_ERROR = 2;
    const SEVERITY_FATAL = 3;

    public function check($valueToCheck):bool;
    public function getErrorMessage(): string;
    public function getSeverity(): int;
}

class StartByRule implements RuleInterface
{
    private $value;
    public function check($valueToCheck):bool{
        $this->value = $valueToCheck;
        return strpos($valueToCheck, 'this') === 0;
    }

    public function getErrorMessage(): string{
        return "Error: \"{$this->value}\" not started by \"test\"";
    }

    public function getSeverity(): int{
        return self::SEVERITY_INFO;
    }
}

class EqualsRule implements RuleInterface
{
    private $value;
    public function check($valueToCheck):bool{
        $this->value = $valueToCheck;
        return strpos($valueToCheck, 'test') === 0;
    }

    public function getErrorMessage(): string{
        return "Error: \"{$this->value}\" not equals to \"test\"";
    }

    public function getSeverity(): int{
        return self::SEVERITY_ERROR;
    }
}


// TEST ----------------------------------------------------------------

$rules = [StartByRule::class, EqualsRule::class];
$errors = [];
$value = 'this is a test';

foreach ($rules as $rule) {
    $rule = new $rule();

    $rule->check($value) ?: $errors[] = $rule->getErrorMessage();
    if(!$rule->check($value) && $rule->getSeverity() === $rule::SEVERITY_FATAL){
        throw new \Exception($rule->getErrorMessage());
    }
}

var_dump($errors);
