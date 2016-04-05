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
        $current_ids = RolePermissionModel::where('role_id', $id)->lists('permission_id')->toArray();
        $delete_ids = array_values(array_diff($current_ids, $permission_ids));
        $create_ids = array_values(array_diff($permission_ids, $current_ids));
        $delete_count = count($delete_ids);
        $create_count = count($create_ids);
        $min_count = min($delete_count, $create_count);

        if ($create_count > $delete_count) {
            //新增多出
            for ($i = $delete_count; $i < $create_count; ++$i) {
                // var_dump('新增多出role_id='.$create_ids[$i]);
                RolePermissionModel::create(['role_id' => $id, 'permission_id' => $create_ids[$i]]);
            }
        } elseif ($create_count < $delete_count) {
            // 删除以前记录中多余的
            $should_delete_ids = array_slice($delete_ids, $min_count, ($delete_count - $create_count));
            RolePermissionModel::where('role_id', $id)->whereIn('permission_id', $should_delete_ids)->delete();
        } else {
        }
        // 按顺序修改原来的关系为新的值
        for ($i = 0; $i < $min_count; ++$i) {
            RolePermissionModel::where(['role_id' => $id, 'permission_id' => $delete_ids[$i]])->update(['permission_id' => $create_ids[$i]]);
        }

        // // TODO: 新增和移除优化
        // RolePermissionModel::where('role_id', $id)->delete();

        // $data['role_id'] = $id;
        // foreach ($permission_ids as $permission_id) {
        //     $data['permission_id'] = $permission_id;
        //     $model = RolePermissionModel::where($data)->first();
        //     if (!$model) { //防止重复添加
        //         RolePermissionModel::create($data);
        //     }
        // }

        return true;
    }
}
