<?php

namespace Wind\Models;

use Wind\Models\Relations\BelongsToManyPermissionsTrait;

/**
 * role model.
 *
 * @author scl <scl@winhu.com>
 */
class RoleModel extends AbstractModel
{
    use BelongsToManyPermissionsTrait;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['name', 'type', 'x_status', 'description'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'role';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The columns to select when displaying an index.
     *
     * @var array
     */
    public static $index = ['id', 'name', 'type'];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $per_page = 20;

    /**
     * The default validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required | min:2 | max:32',
        'type' => 'required',
    ];

    /**
     * 删除当前角色后
     * 1 删除user_role中的关系
     * 2 删除role_permission中的关系
     * 3 更新user表中的role字段.
     *
     * @param bool $return
     */
    public function afterDelete($return)
    {
        if (!$return) {
            throw new Exception('删除异常');
        }

        UserRoleModel::where('role_id', $this->id)->delete();
        RolePermissionModel::where('role_id', $this->id)->delete();
    }
}
