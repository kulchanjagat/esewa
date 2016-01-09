<?php

namespace NikhilPandey\Esewa\Facades;

class Esewa extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'esewa';
    }
}
