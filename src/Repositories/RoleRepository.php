<?php

namespace Wind\Repositories;

use Wind\Models\RolePermissionModel;

/**
 * 权限相关数据模型仓库.
 *
 * @author scl <scl@winhu.com>
 */
class RoleRepository extends AbstractRepository
{
    public function addPermissions($id, array $permission_ids)
    {
        // TODO: 新增和移除优化
        RolePermissionModel::where('role_id', $id)->delete();

        $data['role_id'] = $id;
        foreach ($permission_ids as $permission_id) {
            $data['permission_id'] = $permission_id;
            $model = RolePermissionModel::where($data)->first();
            if (!$model) { //防止重复添加
                RolePermissionModel::create($data);
            }
        }

        return true;
    }
}
