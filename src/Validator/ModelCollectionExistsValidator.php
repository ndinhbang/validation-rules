<?php

namespace Ndinhbang\ValidationRules\Validator;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class ModelCollectionExistsValidator
{
    public function validate($attribute, $value, $parameters, ValidatorContract $validator): bool
    {
        return true;
    }
}
