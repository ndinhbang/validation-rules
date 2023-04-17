<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\DatabaseRule;
use Illuminate\Validation\Validator;

/**
 * @see \Illuminate\Validation\ValidationRuleParser::explodeWildcardRules
 * @see \Illuminate\Validation\Rules\DatabaseRule
 * @see \Illuminate\Validation\Concerns\ReplacesAttributes
 * @see \Illuminate\Validation\Rules\Unique
 * @link https://www.amitmerchant.com/extending-validator-facade-for-custom-validation-rules-in-laravel/
 */
class CollectionExists implements ValidationRule, ValidatorAwareRule
{
    use Conditionable, DatabaseRule;

    /**
     * The validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    protected array $withs = [];
    protected string $blinkKey;
    protected bool $keyable = false;

    /**
     * If true, all items must be exist in database
     */
    protected bool $required;
    protected bool $cacheable;

    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public bool $implicit = false;

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException(
                "Validation value of {$attribute} must be an array"
            );
        }
        $idCollection = collect([]);
        foreach ($this->accessors as $pattern) {
            $idCollection = $idCollection->merge(data_get($value, $pattern));
        }
        // Remove 'falsy' value from array
        $filteredIdCollection = $idCollection->filter();
        if ($this->required
            && $idCollection->count() > $filteredIdCollection->count()) {
            return false;
        }
        // remove duplicated value
        $queryIdCollection = $filteredIdCollection->unique();
        // only check when have data
        if ($queryIdCollection->isEmpty()) {
            return true;
        }

        $records = $this->model
            ->with($this->withs)
            ->whereIn($this->column, $queryIdCollection->all())
            //  Additional wheres
            ->when($this->wheres, function ($query, $wheres) {
                $query->where($wheres);
            })
            // cache query
            ->when($this->cacheable, function ($query) {
                $query->cacheFor(config('cache.ttl'));
            })
            ->get();

        if ($queryIdCollection->count() > $records->count()) {
            return false;
        }

        // save to request input to use latter on controller
        $blinkKey = $this->blinkKey ?? "__{$this->model->getTable()}__";
        // remember for current request
        blink()->forever(
            $blinkKey,
            $records->when($this->keyable, function ($collection) {
                return $collection->keyBy($this->column);
            })
        );

        return true;
    }

    /**
     * @param $key
     * @return $this
     */
    public function key($key): static
    {
        $this->blinkKey = $key;

        return $this;
    }

    /**
     * The collection save to blink cache will be keyed by
     * @param $keyable
     * @return $this
     */
    public function keyable($keyable): static
    {
        $this->keyable = $keyable;

        return $this;
    }

    /**
     * @param array $withs
     * @return $this
     */
    public function with(array $withs): static
    {
        $this->withs = $withs;

        return $this;
    }

    public function wheres(\Closure|array $wheres): static
    {
        $this->wheres = $wheres;

        return $this;
    }

    public function cacheable(bool $cacheable): static
    {
        $this->cacheable = $cacheable;
        return $this;
    }

    public function required(bool $required): static
    {
        $this->required = $required;
        return $this;
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return ":attribute chứa giá trị không hợp lệ";
    }

    public function setValidator(Validator $validator)
    {
        // TODO: Implement setValidator() method.
    }
}
