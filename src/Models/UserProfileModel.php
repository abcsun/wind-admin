<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * user model.
 *
 * @author scl <scl@winhu.com>
 */
class UserProfileModel extends AbstractModel
{
    use SoftDeletes;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'user_profile';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['user_id'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'user_profile';

    /**
     * The properties on the model that are dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * 验证规则集.
     *
     * @var array
     */
    public static $rules = [];
}
