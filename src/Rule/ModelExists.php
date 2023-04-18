<?php

namespace Ndinhbang\ValidationRules\Rule;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rules\DatabaseRule;

class ModelExists implements ValidationRule
{
    use Conditionable, DatabaseRule;

    /**
     * Determine if the validation rule passes.
     * @reference https://laravel.com/docs/9.x/queries#where-clauses
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
//        $record = $this->model
//            ->where($this->column, $value)
//            //  Additional wheres
//            ->when($this->wheres, function ($query, $wheres) {
//                $query->where($wheres);
//            })
//            // cache query
//            ->when($this->cacheable, function ($query) {
//                $query->cacheFor(config('cache.ttl'));
//            })
//            ->first();
//
//        if (is_null($record) && $this->required) {
////            $this->errorMessage = ":attribute {$value} không tồn tại";
//            return false;
//        }
//        // save to request input to use latter on controller
//        $blinkKey = $this->blinkKey ?? '__' . Str::singular($this->model->getTable())  . '__';
//        // remember for current request
//        blink()->forever($blinkKey, $record);
//
//        return true;
    }

}
