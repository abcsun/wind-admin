<?php

namespace Wind\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the user repository facade class.
 *
 * @author sun
 */
class UserRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user_repository';
    }
}
