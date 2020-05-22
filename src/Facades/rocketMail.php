<?php

namespace rocketfy\rocketMail\Facades;

use Illuminate\Support\Facades\Facade;

class rocketMail extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'rocketmail';
    }
}
