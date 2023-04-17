<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @reference \Illuminate\Validation\ValidationRuleParser::explodeWildcardRules
 * @reference \Illuminate\Validation\Rules\DatabaseRule
 * @reference \Illuminate\Validation\Concerns\ReplacesAttributes
 * @reference \Illuminate\Validation\Rules\Unique
 * @link https://www.amitmerchant.com/extending-validator-facade-for-custom-validation-rules-in-laravel/
 */
class CollectionExists implements ValidationRule
{

    protected Model $model;
    protected string $column;
    protected array $accessors;
    protected \Closure|array $wheres;
    protected array $withs = [];
    protected string $blinkKey;
    protected bool $keyable = false;

    /**
     * If true, all items must be exist in database
     */
    protected bool $required;
    protected bool $cacheable;
    protected ?string $errorMessage = null;

    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public bool $implicit = false;

    /**
     * @throws \Exception|\Throwable
     */
    public function __construct(
        string         $modelClass,
        array|string   $accessors = '*.id',
        string         $column = null,
        \Closure|array $wheres = [],
        bool           $required = true,
        bool           $cacheable = true
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
        $this->accessors = is_array($accessors) ? $accessors : [$accessors];
        $this->wheres = $wheres;
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
}
