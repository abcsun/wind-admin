<?php
/**
 * Created by PhpStorm.
 * Date: 16/3/14
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the user repository facade class.
 *
 * @author sun
 */
class ConfigRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config_repository';
    }
}
