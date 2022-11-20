<?php

namespace Components;

use Constants\Rules;
use Exception;
use CustomExceptions\ValidationException;

class ValidationComponent
{
    /**
     * each rule in the `$rules` array has corresponding method in component starts with `validate_rule_`.
     * the rules in component are scalable. you can make your rule by add it in the array,
     * then create the logic in the corresponding method starts with `validate_rule_`.
     * @var string[] $rules
     */
    private array $rules;

    public function __construct()
    {
        $this->rules = (new Rules())->list();
    }

    /**
     * @param array $schema
     * @param array $values
     * @param boolean $level
     * @param boolean $rule_is_list
     * @return void
     * @throws Exception if there is unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */

    private function validate($schema, $values, $level, $rule_is_list=true): void
    {
        foreach ($schema as $key => $rules)
        {
            if (!$rule_is_list && is_array($rules))
            {
                throw new Exception("`$key` rule can't be an array.");
            }
            elseif ($rule_is_list && ! is_array($rules))
            {
                throw new Exception("`$key` validation should be array of rules.");
            }

            if(!$rule_is_list && ! is_array($rules))
            {
                // to adapt with the following logic in this case
                $rules = [$rules];
            }

            foreach ($rules as $rule)
            {
                if(! in_array($rule, $this->rules))
                {
                    throw new Exception("this rule ($rule) isn't listed in constants.Rules.");
                }

                $rule = $this->handleRuleConvention($rule);
                $this->validateIfRuleIsExists($rule);

                if (! $rule_is_list) {
                    $value = array_shift($values);
                }
                else {
                    $value = $values[$key] ?? null;
                }

                $this->$rule(
                    $value,
                    "$key ($level)"
                );
            }
        }

    }

    /**
     * rules in `$urlParamsValidationSchema` should be ordered according the passed `$url_params` to handle correct.
     * @param array $validationSchema
     * @param array $values
     * @return void
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateUrlParams(array $validationSchema, array $values): void
    {
        $this->validate($validationSchema, $values, "url params", false);
    }

    /**
     * @param array $validationSchema
     * @param array $values
     * @return void
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateQueryParams(array $validationSchema, array $values): void
    {
        $this->validate($validationSchema, $values, "query params");
    }

    /**
     * @param array $validationSchema
     * @param array $values
     * @return void
     * @throws Exception if they are unimplemented rule
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateRequestPayload(array $validationSchema, array $values): void
    {
        $this->validate($validationSchema, $values, "request payload");
    }

    /**
     * @param $rule
     * @return void
     * @throws Exception if rule hasn't implementation.
     */
    private function validateIfRuleIsExists($rule): void
    {
        $rule = $this->handleRuleConvention($rule);

        if (! method_exists($this, $rule))
        {
            throw new Exception("rule $rule hasn't implementation.");
        }

    }

    /**
     * @param $rule
     * @return string
     * @throws Exception if $rule doesn't start with 'validate_rule_'
     */
    private function handleRuleConvention($rule): string
    {
        $missing_prefix = "";

        if (! str_contains($rule, 'validate_'))
        {
            $missing_prefix = 'validate_';
        }

        if (! str_contains($rule, 'rule_'))
        {
            $missing_prefix .= 'rule_';
        }

        return $missing_prefix . $rule;
    }

    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_required($value, $param_name): void
    {
        if ($value === null)
        {
            throw new ValidationException("$param_name is required.");
        }
    }

    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_integer($value, $param_name): void
    {
        if (! ctype_digit($value))
        {
            throw new ValidationException("$param_name should be an integer.");
        }
    }

    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string($value, $param_name): void
    {
        if ($value && (is_numeric($value) || in_array($value, ["true", "false"]) || gettype($value) != "string"))
        {
            throw new ValidationException("$param_name should be string.");
        }
    }

    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string_not_empty($value, $param_name): void
    {
        if (! $value)
        {
            throw new ValidationException("$param_name should not be empty.");
        }

        $this->validate_rule_string($value, $param_name);
    }

    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_boolean($value, $param_name): void
    {
        if ($value && gettype($value) != "boolean")
        {
            throw new ValidationException("$param_name should be boolean.");
        }
    }
    /**
     * @param $value
     * @param $param_name
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_email($value, $param_name): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("$param_name invalid email format.");
        }

    }

}