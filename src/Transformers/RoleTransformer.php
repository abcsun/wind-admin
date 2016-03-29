<?php

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\RoleModel as Role;

/**
 * UserTransformer RoleModel数据转换为固定格式.
 *
 * @author sun <scl@winhu.com>
 */
class RoleTransformer extends TransformerAbstract
{
    /**
     * 所有可扩展的数据项.
     *
     * @var array
     */
    protected $availableIncludes = [
        'permissions',
    ];

    /**
     * 自动加载的关联资源（使用预加载减少数据库请求次数）.
     *
     * @var array
     */
    protected $defaultIncludes = [
        'permissions',
    ];

    /**
     * 用于数据转换为固定格式.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => (int) $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'x_status' => $role->x_status,
            'user_count' => 9999,
            'type' => $role->type,
            'permissions' => $role->permissions,
            'created_at' => $role->created_at,
            'updated_at' => $role->updated_at,
            'deleted_at' => $role->deleted_at,
        ];
    }

    /**
     * Include Permissions.
     *
     * @return League\Fractal\ItemResource
     */
    public function includePermissions(Role $role)
    {
        // $permissions = $role->permissions;
    }
}
