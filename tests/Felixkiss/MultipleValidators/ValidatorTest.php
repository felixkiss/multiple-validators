<?php

use Symfony\Component\Translation\TranslatorInterface;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Felixkiss\MultipleValidators\Validator;

class ValidatorTest extends TestCase
{
    function setUp()
    {
        $translator = Mockery::mock(TranslatorInterface::class);
        $data = [];
        $rules = [];
        $messages = [];
        $customAttributes = [];

        $this->validator = new Validator($translator, $data, $rules, $messages, $customAttributes);
    }

    function testValidatorProxiesMethodCallToChildValidator()
    {
        $childValidator = Mockery::mock(ValidatorContract::class);
        $childValidator->shouldReceive('validateFoo')->times(1);

        $this->validator->addValidator($childValidator);
        $this->validator->validateFoo();
    }
}
