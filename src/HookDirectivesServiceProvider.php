<?php

declare(strict_types=1);

namespace Esemve\Hook;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class HookDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootDirectives();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot blade directives.
     */
    protected function bootDirectives(): void
    {
        Blade::directive('hook', function ($parameter): string {
            $parameter = \trim($parameter, '()');
            $parameters = \explode(',', $parameter);

            $name = \trim($parameters[0], "'");

            // $parameters[1] => bool => is this wrapper component?
            if (!isset($parameters[1])) {
                return ' <'.'?php

                $__definedVars = (get_defined_vars()["__data"]);
                if (empty($__definedVars))
                {
                    $__definedVars = [];
                }
                $output = \Hook::get("template.'.$name.'",["data"=>$__definedVars],function($data) { return null; });
                if ($output)
                echo $output;
                ?'.'>';
            } else {
                return ' <'.'?php
                    $__hook_name="'.$name.'";
                    ob_start();
                ?'.'>';
            }
        });

        Blade::directive('endhook', function ($parameter): string {
            return ' <'.'?php
                $__definedVars = (get_defined_vars()["__data"]);
                if (empty($__definedVars))
                {
                    $__definedVars = [];
                }
                $__hook_content = ob_get_clean();
                $output = \Hook::get("template.$__hook_name",["data"=>$__definedVars],function($data) { return null; },$__hook_content);
                unset($__hook_name);
                unset($__hook_content);
                if ($output)
                echo $output;
                ?'.'>';
        });
    }
}
