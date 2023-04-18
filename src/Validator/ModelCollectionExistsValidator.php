<?php

namespace Ndinhbang\ValidationRules\Validator;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\InvokableValidationRule;
use Ndinhbang\ValidationRules\Rule\ModelCollectionExists;

class ModelCollectionExistsValidator
{
    public function validate($attribute, $value, $parameters, ValidatorContract $validator): bool
    {
        $validator->requireParameterCount(1, $parameters, 'model_collection_exists');

        $parameters = $validator->parseNamedParameters($parameters);

        return InvokableValidationRule::make(new ModelCollectionExists(...$parameters))
            ->passes($attribute, $value);
    }
}
