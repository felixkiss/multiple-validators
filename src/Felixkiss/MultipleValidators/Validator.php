<?php namespace Felixkiss\MultipleValidators;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\Validator as BaseValidator;
use Illuminate\Support\Str;

class Validator extends BaseValidator
{
    /**
     * @var \Illuminate\Contracts\Validation\Validator[]
     */
    private $validators;

    public function addValidator(ValidatorContract $validator)
    {
        $validator->setPresenceVerifier($this->getPresenceVerifier());
        $this->addMessages($validator->customMessages);
        $this->addReplacements($validator);

        $this->validators[] = $validator;
    }

    public function __call($method, $arguments)
    {
        foreach ($this->validators as $validator) {
            if (method_exists($validator, $method)) {
                return call_user_func_array([$validator, $method], $arguments);
            }
        }

        return parent::__call($method, $arguments);
    }

    private function addMessages(array $messages = [])
    {
        $this->customMessages = array_merge($this->customMessages, $messages);
    }

    private function addReplacements(ValidatorContract $validator)
    {
        if (get_class($validator) === BaseValidator::class) {
            return;
        }

        $methods = get_class_methods($validator);
        $self = $this;
        $replaceMethods = array_filter($methods, function($method) use ($self) {
            return Str::startsWith($method, 'replace') && !method_exists($self, $method);
        });

        $replacers = [];
        foreach ($replaceMethods as $method) {
            $rule = Str::substr($method, 7);
            $replacers[$rule] = function() use ($validator, $method) {
                return call_user_func_array([$validator, $method], func_get_args());
            };
        }

        $this->addReplacers($replacers);
    }
}
