<?php

namespace App\Php\ValidationRules;

use App\Php\ValidationRules\Rules\Rule;
use App\Php\ValidationRules\Rules\Severity;

class RulesChecker
{
    private array $errorContainer = [];

    public static function create(?array $rules = []): self
    {
        return new static($rules);
    }

    private function __construct(private array $rules)
    {

    }

    /**
     *
     * @param $value
     * @return bool
     */
    public function check($value):bool
    {
        /** @var Rule $rule */
        foreach ($this->rules as $rule) {
            if(!$rule->check($value)) {
                $this->errorContainer[$rule->getSeverity()] = $rule->getErrorMessage();
            }
        }

        return !!count($this->errorContainer);
    }

    /**
     * @param array $rules
     * @return $this
     */
    public function addRules(array $rules): self
    {
        array_push($this->rules, ...$rules);
        return $this;
    }

    /**
     * @return array
     */
    public function getErrors():array
    {
        return $this->errorContainer;
    }
}
