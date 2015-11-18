<?php

namespace Fadion\Fixerio\Facades;

use Illuminate\Support\Facades\Facade;

class Exchange extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'exchange';
    }

}