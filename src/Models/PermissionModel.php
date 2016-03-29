<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Wind\Models\Relations\BelongsToManyGrantApisTrait;

/**
 * permission model.
 *
 * @author scl <scl@winhu.com>
 */
class PermissionModel extends AbstractModel
{
    use BelongsToManyGrantApisTrait, SoftDeletes;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'permission';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['pid', 'slug', 'name', 'url', 'sort', 'type', 'display_name', 'description', 'x_status'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'permission';

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
    public static $index = ['id', 'pid', 'slug', 'name', 'url', 'sort', 'type', 'display_name', 'description', 'x_status'];

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
        'slug' => 'required | min:2 | max:32',
        'type' => 'required',
    ];

    /**
     * 用户基本信息插入完成后续处理，生成对应的profile表记录.
     *
     * @param array $input  [description]
     * @param Model $return [description]
     *
     * @return [type] [description]
     */
    public static function afterCreate(array $input, Model $return)
    {
    }

    /**
     * 修改用户信息前预处理.
     *
     * @param array $input
     */
    public function beforeUpdate(array $input)
    {
    }

    /**
     * 删除当前权限后
     * 1 删除所有下级permission
     * 2 删掉关联API、以及角色中的关系.
     *
     * @param bool $return
     */
    public function afterDelete($return)
    {
        if (!$return) {
            throw new Exception('删除异常');
        }

        PermissionGrantApiModel::where('permission_id', $this->id)->delete();
        RolePermissionModel::where('permission_id', $this->id)->delete();
    }
}
