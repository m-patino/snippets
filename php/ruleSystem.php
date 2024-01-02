<?php

/**
 * @todo Need to be improve
 * - Add ErrorBags class for a better errors support
 * - Add Interaction between Rules (ex: if RuleA KO skip RuleAA)
 */

 /**
  * I use an interface for define a class patern for each *Rule.php file
  * and i add some const like a enum, because this const it's use only in this context
  */
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

/**
 * Rule exemple 
 */
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

/**
 * Rule exemple 
 */
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

/**
 * This part it's only for test
 * We can use it like a Service (symfony) or a  helper. 
 * For the exemple i use an array for define my list of rules to apply,
 * but why not used a json value ( database, file ...) or a glob who target Rules directory ????
 */
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
