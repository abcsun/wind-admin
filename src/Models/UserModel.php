<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Wind\Models\Relations\HasOneProfileTrait;
use Wind\Models\Relations\BelongsToManyRolesTrait;

/**
 * user model.
 *
 * @author scl <scl@winhu.com>
 */
class UserModel extends AbstractModel implements
    AuthenticatableContract,
    AuthorizableContract,
    JWTSubject
{
    use BelongsToManyRolesTrait, HasOneProfileTrait, SoftDeletes, Authenticatable, Authorizable;

    /**
     * 表名.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 可批量修改字段.
     *
     * @var string
     */
    protected $fillable = ['phone', 'open_id', 'name', 'password', 'gravatar', 'x_status', 'role'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'user';

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
    public static $index = ['id', 'phone', 'name', 'gravatar'];

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
        'phone' => 'required |unique:user,phone,NULL,id,deleted_at,NULL|digits:11',
        'name' => 'required | min:2 | max:32',
        'password' => 'required | min:6',
        'code' => 'required',
    ];

    /**
     * Access caches.
     *
     * @var array
     */
    protected $access = [];

    /**
     * 对用户密码进行password_hash加密.
     *
     * @param string $value [description]
     */
    public function setPasswordAttribute($value = 'wind')
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

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
        $data['user_id'] = $return['id'];
        UserProfileModel::create($data);
    }
    /**
     * 修改用户信息前预处理.
     *
     * @param array $input
     */
    public function beforeUpdate(array $input)
    {
        // echo 'beforeUpdate';
    }

    /**
     * 删除用户后
     * 1 删除user_profile中的记录
     * 2 删除user_role中的关系.
     *
     * @param bool $return
     */
    public function afterDelete($return)
    {
        if (!$return) {
            throw new Exception('删除异常');
        }

        UserProfileModel::where('user_id', $this->id)->delete();
        UserRoleModel::where('user_id', $this->id)->delete();
    }
}
