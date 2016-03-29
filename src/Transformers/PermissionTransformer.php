<?php

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\PermissionModel as Permission;

/**
 * PermissionTransformer.
 *
 * @author sun <scl@winhu.com>
 */
class PermissionTransformer extends TransformerAbstract
{
    /**
     * 所有可扩展的数据项.
     *
     * @var array
     */
    protected $availableIncludes = [
        'grant_apis',
    ];

    /**
     * 将Model数据转换为固定格式.
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id' => (int) $permission->id,
            'pid' => (int) $permission->id,
            'name' => $permission->name,
            'type' => $permission->type,
            'slug' => $permission->slug,
            'display_name' => $permission->display_name,
            'description' => $permission->description,
            'url' => $permission->url,
            'sort' => $permission->sort,
            'x_status' => $permission->x_status,
            'created_at' => $permission->created_at,
            'updated_at' => $permission->updated_at,
            'deleted_at' => $permission->deleted_at,
            'grant_apis' => $permission->grant_apis,
        ];
    }
}
