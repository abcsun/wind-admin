<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Wind\Models\Relations\HasOneProfileTrait;

/**
 * grant api model.
 *
 * @author scl <scl@winhu.com>
 */
class GrantApiModel extends AbstractModel
{
    use HasOneProfileTrait, SoftDeletes;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'grant_api';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['slug', 'path', 'method', 'name', 'x_status'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'grant_api';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The revisionable columns.
     *
     * @var array
     */
    // protected $keepRevisionOf = ['phone', 'password', 'activated', 'last_login', 'first_name', 'last_name'];

    /**
     * The columns to select when displaying an index.
     *
     * @var array
     */
    public static $index = ['slug', 'path', 'method', 'name'];

    /**
     * The max users per page when displaying a paginated index.
     *
     * @var int
     */
    public static $paginate = 20;

    /**
     * The user validation rules.
     *
     * @var array
     */
    public static $rules = [
        'slug' => 'required | unique:grant_api,slug,NULL,id,deleted_at,NULL',
        'name' => 'required | min:2 | max:128',
    ];

    /**
     * 删除当前API后
     * 1 删除permission_grant_api中的关系.
     *
     * @param bool $return
     */
    public function afterDelete($return)
    {
        if (!$return) {
            throw new Exception('删除异常');
        }

        PermissionGrantApiModel::where('grant_api_id', $this->id)->delete();
    }
}
