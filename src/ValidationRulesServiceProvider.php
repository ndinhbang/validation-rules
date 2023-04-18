<?php

namespace Ndinhbang\ValidationRules;

use Ndinhbang\ValidationRules\Validator\ModelExistsValidator;
use Ndinhbang\ValidationRules\Validator\ModelCollectionExistsValidator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationRulesServiceProvider extends ServiceProvider
{
    protected array $validators = [
        'model_collection_exists' => ModelCollectionExistsValidator::class,
        'model_exists' => ModelExistsValidator::class,
    ];
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        foreach ($this->validators as $name => $class) {
            Validator::extend($name, $class);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('validation-rules.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'validation-rules');
    }
}
