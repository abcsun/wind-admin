<?php

namespace Wind\Repositories;

use Wind\Models\PermissionGrantApiModel;

/**
 * 权限相关数据模型仓库.
 *
 * @author scl <scl@winhu.com>
 */
class PermissionRepository extends AbstractRepository
{
    /**
     * 给指定权限增加授权api.
     *
     * @param [type] $id      [description]
     * @param array  $api_ids [description]
     */
    public function addGrantApis($id, array $api_ids)
    {
        // TODO: 新增和移除优化
        PermissionGrantApiModel::where('permission_id', $id)->delete();

        $data['permission_id'] = $id;
        foreach ($api_ids as $api_id) {
            $data['grant_api_id'] = $api_id;
            $model = PermissionGrantApiModel::where($data)->first();
            if (!$model) { //防止重复添加
                PermissionGrantApiModel::create($data);
            }
        }

        return true;
    }
}
