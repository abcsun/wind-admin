<?php

namespace Wind\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the role repository facade class.
 *
 * @author sun
 */
class RoleRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'role_repository';
    }
}
