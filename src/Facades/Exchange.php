<?php

namespace Fadion\Fixerio\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fadion\Fixerio\Exchange
 */
class Exchange extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'exchange'; }

}