<?php

namespace Wind\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the permission repository facade class.
 *
 * @author sun
 */
class GrantApiRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'grant_api_repository';
    }
}
