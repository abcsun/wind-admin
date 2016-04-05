<?php

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\UserModel as User;
use League\Fractal\Serializer\ArraySerializer;

/**
 * UserTransformer 用户UserModel数据转换为固定格式.
 *
 * @author sun <scl@winhu.com>
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * 所有可扩展的数据项.
     *
     * @var array
     */
    protected $availableIncludes = [
        'roles',
        'profile',
    ];

    /**
     * 自动加载的关联资源（使用预加载减少数据库请求次数）.
     *
     * @var array
     */
    protected $defaultIncludes = [
        'roles',
        'profile',
    ];

    /**
     * 将UserModel数据转换为固定格式.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'open_id' => $user->open_id,
            'gravatar' => $user->gravatar,
            'role' => $user->role,
            'x_status' => $user->x_status,
            'profile' => $user->profile,
            'roles' => $user->roles,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => $user->deleted_at,
        ];
    }

    /**
     * Include Roles 数据转换.
     *
     * @return League\Fractal\ItemResource
     */
    public function includeRoles(User $user)
    {
        // return $user->roles;

        // return $this->collection($roles, new RoleTransformer(), [], function ($resource, $fractal) {
        //     $fractal->setSerializer(new ArraySerializer());
        // });
    }

    /**
     * Include profile 数据转换.
     *
     * @return League\Fractal\ItemResource
     */
    public function includeProfile(User $user)
    {
        // return $profile = $user->profile;
    }
}
