<?php

namespace Wind\Transformers;

use League\Fractal\TransformerAbstract;
use Wind\Models\GrantApiModel as API;

/**
 * UserTransformer RoleModel数据转换为固定格式.
 *
 * @author sun <scl@winhu.com>
 */
class GrantApiTransformer extends TransformerAbstract
{
    /**
     * 所有可扩展的数据项.
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * 用于数据转换为固定格式.
     *
     * @return array
     */
    public function transform(API $api)
    {
        return [
            'id' => (int) $api->id,
            'name' => $api->name,
            'slug' => $api->slug,
            'x_status' => $api->x_status,
            'created_at' => $api->created_at,
            'updated_at' => $api->updated_at,
            'deleted_at' => $api->deleted_at,
        ];
    }
}
