<?php

namespace Esemve\Hook\Facades;

use Esemve\Hook\Hook;
use Illuminate\Support\Facades\Facade;

class HookFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Hook::class;
    }
}
