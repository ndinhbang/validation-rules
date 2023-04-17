<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModelExists implements ValidationRule
{
    protected Model $model;
    protected string $column;
    protected array $wheres = [];
    protected string $blinkKey;
    protected bool $required;
    protected bool $cacheable;
    protected ?string $errorMessage = null;

    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public bool $implicit = true;

    /**
     * @throws \Throwable
     */
    public function __construct(
        string $modelClass,
        string $column = null,
        array $wheres = [],
        bool $required = true,
        bool $cacheable = true
    )
    {
        throw_if(
            !is_subclass_of($modelClass, Model::class),
            new \InvalidArgumentException(
                "{$modelClass} must be a model"
            )
        );

        $this->model = resolve($modelClass);
        $this->column = $column ?? $this->model->getRouteKeyName();
        $this->required = $required;
        $this->cacheable = $cacheable;
        $this->wheres = $wheres;
    }

    /**
     * Determine if the validation rule passes.
     * @reference https://laravel.com/docs/9.x/queries#where-clauses
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $record = $this->model
            ->where($this->column, $value)
            //  Additional wheres
            ->when($this->wheres, function ($query, $wheres) {
                $query->where($wheres);
            })
            // cache query
            ->when($this->cacheable, function ($query) {
                $query->cacheFor(config('cache.ttl'));
            })
            ->first();

        if (is_null($record) && $this->required) {
//            $this->errorMessage = ":attribute {$value} không tồn tại";
            return false;
        }
        // save to request input to use latter on controller
        $blinkKey = $this->blinkKey ?? '__' . Str::singular($this->model->getTable())  . '__';
        // remember for current request
        blink()->forever($blinkKey, $record);

        return true;
    }

    public function key($key): static
    {
        $this->blinkKey = $key;
        return $this;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return ":attribute :value không tồn tại";
    }

}
