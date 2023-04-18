<?php

namespace Ndinhbang\ValidationRules\Rule;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\DatabaseRule;
use Illuminate\Validation\Validator;
use Ndinhbang\ValidationRules\Concerns\ValidatorAware;

/**
 * @see \Illuminate\Validation\Validator::passes
 * @see \Illuminate\Validation\Validator::validateAttribute
 * @see \Illuminate\Validation\Validator::validateUsingCustomRule
 * @see \Illuminate\Validation\ValidationRuleParser::prepareRule
 * @see \Illuminate\Validation\InvokableValidationRule::passes
 * @see \Illuminate\Validation\Rules\DatabaseRule
 * @see \Illuminate\Validation\Concerns\ReplacesAttributes
 * @see \Illuminate\Validation\Validator::validateExists
 * @see \Illuminate\Validation\Rules\Unique
 * @see \Illuminate\Validation\Rules\Exists
 * @link https://www.amitmerchant.com/extending-validator-facade-for-custom-validation-rules-in-laravel/
 */
class ModelCollectionExists implements ValidationRule, ValidatorAwareRule
{
    use Conditionable,
        DatabaseRule,
        ValidatorAware;

    /**
     * @var bool
     */
    public bool $implicit = false;

    /**
     * Create a new rule instance.
     *
     * @param string $table
     * @param string $column
     * @return void
     */
    public function __construct($table, $column = 'NULL')
    {
        $this->column = $column;

        $this->table = $this->resolveTableName($table);
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
//        if (!is_array($value)) {
//            throw new \InvalidArgumentException(
//                "Validation value of {$attribute} must be an array"
//            );
//        }
//        $idCollection = collect([]);
//        foreach ($this->accessors as $pattern) {
//            $idCollection = $idCollection->merge(data_get($value, $pattern));
//        }
//        // Remove 'falsy' value from array
//        $filteredIdCollection = $idCollection->filter();
//        if ($this->required
//            && $idCollection->count() > $filteredIdCollection->count()) {
//            return false;
//        }
//        // remove duplicated value
//        $queryIdCollection = $filteredIdCollection->unique();
//        // only check when have data
//        if ($queryIdCollection->isEmpty()) {
//            return true;
//        }
//
//        $records = $this->model
//            ->with($this->withs)
//            ->whereIn($this->column, $queryIdCollection->all())
//            //  Additional wheres
//            ->when($this->wheres, function ($query, $wheres) {
//                $query->where($wheres);
//            })
//            // cache query
//            ->when($this->cacheable, function ($query) {
//                $query->cacheFor(config('cache.ttl'));
//            })
//            ->get();
//
//        if ($queryIdCollection->count() > $records->count()) {
//            return false;
//        }
//
//        // save to request input to use latter on controller
//        $blinkKey = $this->blinkKey ?? "__{$this->model->getTable()}__";
//        // remember for current request
//        blink()->forever(
//            $blinkKey,
//            $records->when($this->keyable, function ($collection) {
//                return $collection->keyBy($this->column);
//            })
//        );
//
//        return true;
    }

}
