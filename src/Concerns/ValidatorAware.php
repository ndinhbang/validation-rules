<?php

namespace Ndinhbang\ValidationRules\Concerns;

use Illuminate\Validation\Validator;

trait ValidatorAware
{
    /**
     * The validator instance.
     *
     * @var Validator
     */
    protected Validator $validator;

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }
}
