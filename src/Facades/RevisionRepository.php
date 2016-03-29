<?php
/**
 * Date: 16/3/22
 * Author: eric <eric@winhu.com>.
 */

namespace Wind\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * This is the permission repository facade class.
 *
 * @author sun
 */
class RevisionRepository extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'revision_repository';
    }
}
