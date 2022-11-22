<?php

namespace Components;

use Constants\Rules;
use Exception;
use CustomExceptions\ValidationException;
use Illuminate\Database\Eloquent\Model;

class ValidationComponent
{
    /**
     * each rule in the `$rules` array has corresponding method in component starts with `validate_rule_`.
     * the rules in component are scalable. you can make your rule by add it in the array,
     * then create the logic in the corresponding method starts with `validate_rule_`.
     * @var string[] $rules
     */
    private array $rules;

    private $resource_id;

    public function __construct()
    {
        $this->rules = (new Rules())->list();
    }

    /**
     * @throws Exception if there is unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */

    private function validate($schema, $values, $level, $values_are_positional=false): void
    {
        foreach ($schema as $key => $rules)
        {
            if (! is_array($rules))
            {
                throw new Exception("`$key` validation should be array of rules.");
            }

            foreach ($rules as $index => $rule)
            {
                /**
                 * handle special rule like 'unique'
                 * when rule has extra details
                 * the rule will be stored as associated array
                 * then the key will be the rule
                 * the value will be the rule details
                 */
                if (! is_numeric($index))
                {
                    $rule_details = $rule;
                    $rule = $index;
                }

                if(! in_array($rule, $this->rules))
                {
                    throw new Exception("this rule ($rule) isn't listed in constants.Rules.");
                }

                $rule = $this->handleRuleConvention($rule);
                $this->validateIfRuleIsExists($rule);

                if ($values_are_positional) {
                    $value = array_shift($values);
                }
                else {
                    $value = $values[$key] ?? null;
                }

                $arguments = [$value, $key, $level];

                if (isset($rule_details))
                {
                    $arguments[] = $rule_details;
                }

                $this->$rule(...$arguments);
            }
        }

    }

    /**
     * rules in `$urlParamsValidationSchema` should be ordered according the passed `$url_params` to handle correct.
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateUrlParams(array $validationSchema, array $values): void
    {
        if ($values) {
            $this->resource_id = end($values);
        }

        $this->validate($validationSchema, $values, "url params", true);
    }

    /**
     * @throws Exception if they are unimplemented rule
     * @throws Exception if one of the rule isn't listed in constants.Rules
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateQueryParams(array $validationSchema, array $values, $resource_id=null): void
    {
        if ($resource_id) {
            $this->resource_id = $resource_id;
        }

        $this->validate($validationSchema, $values, "query params");
    }

    /**
     * @throws Exception if they are unimplemented rule
     * @throws ValidationException if one of the registered rules is failed
     */
    public function validateRequestPayload(array $validationSchema, array $values, $resource_id=null): void
    {
        if ($resource_id) {
            $this->resource_id = $resource_id;
        }

        $this->validate($validationSchema, $values, "request payload");
    }

    /**
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
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_required($value, $param, $level): void
    {
        if ($value === null)
        {
            throw new ValidationException("$param ($level) is required.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_integer($value, $param, $level): void
    {
        if ($value && ! ctype_digit($value))
        {
            throw new ValidationException("$param ($level) should be an integer.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string($value, $param, $level): void
    {
        if ($value && (is_numeric($value) || in_array($value, ["true", "false"]) || gettype($value) != "string"))
        {
            throw new ValidationException("$param ($level) should be string.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_string_not_empty($value, $param, $level): void
    {
        if ($value === "")
        {
            throw new ValidationException("$param ($level) should not be empty.");
        }

        $this->validate_rule_string($value, $param, $level);
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_boolean($value, $param, $level): void
    {
        if ($value && gettype($value) != "boolean")
        {
            throw new ValidationException("$param ($level) should be boolean.");
        }
    }

    /**
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_email($value, $param, $level): void
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("$param ($level) invalid email format.");
        }

    }

    /**
     * @throws Exception if `resource` not passed with values.
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_unique($value, $param, $level, $rule_details): void
    {
        if (! key_exists('resource', $rule_details)) {
            throw new Exception("the 'resource' is required with `unique` rule.");
        }
        elseif (! $param && ! key_exists('field', $rule_details)) {
            throw new Exception("the 'field' is required with `unique` rule.");
        }

        if(! $value)
        {
            return;
        }

        $field = $rule_details['field'] ?? $param;

        /**
         * @var Model $resource
         */
        $resource = $rule_details['resource'];

        $query =
            $resource::query()
                ->where($field, $value);

        if ($this->resource_id)
        {
            $query->where('id', '!=', $this->resource_id);
        }

        if ($query->count() >= 1) {
            throw new ValidationException("'$value' as $param isn't unique ($level).");
        }
    }

    /**
     * Not working with parent resource!
     * @throws Exception if `resource` not passed with values.
     * @throws ValidationException if rule is failed.
     */
    private function validate_rule_exists($value, $param, $level, $rule_details): void
    {
        if (! key_exists('resource', $rule_details)) {
            throw new Exception("the 'resource' is required with `unique` rule.");
        }

        if(! $this->resource_id)
        {
            return;
        }

        /**
         * @var Model $resource
         */
        $resource = $rule_details['resource'];

        $existed =
            $resource::query()
                ->where('id', $this->resource_id)
                ->exists();


        if (! $existed) {
            throw new ValidationException("$param ($level) isn't exist.");
        }
    }
}