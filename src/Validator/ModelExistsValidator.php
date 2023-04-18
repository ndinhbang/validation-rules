<?php

namespace Ndinhbang\ValidationRules\Validator;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class ModelExistsValidator
{
    public function validate($attribute, $value, $parameters, ValidatorContract $validator): bool
    {

        return true;
    }
}
