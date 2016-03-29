<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * role permission relation model.
 *
 * @author scl <scl@winhu.com>
 */
class RolePermissionModel extends AbstractModel
{
    use SoftDeletes;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'role_permission';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['permission_id', 'role_id', 'x_status'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'role_permission';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $per_page = 20;
}
