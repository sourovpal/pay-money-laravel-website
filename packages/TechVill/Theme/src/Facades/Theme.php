<?php

namespace TechVill\Theme\Facades;

use Illuminate\Support\Facades\Facade;

class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \TechVill\Theme\Contracts\ThemeContract::class;
    }
}
