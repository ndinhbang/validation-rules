<?php

namespace Ndinhbang\ValidationRules\Validator;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\InvokableValidationRule;
use Ndinhbang\ValidationRules\Rule\ModelExists;

class ModelExistsValidator
{
    public function validate($attribute, $value, $parameters, ValidatorContract $validator): bool
    {
        $validator->requireParameterCount(1, $parameters, 'model_exists');

        $parameters = $validator->parseNamedParameters($parameters);

        return InvokableValidationRule::make(new ModelExists(...$parameters))
            ->passes($attribute, $value);
    }
}
