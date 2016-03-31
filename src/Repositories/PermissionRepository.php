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
        $current_ids = PermissionGrantApiModel::where('permission_id', $id)->lists('grant_api_id')->toArray();
        $delete_ids = array_values(array_diff($current_ids, $api_ids));
        $create_ids = array_values(array_diff($api_ids, $current_ids));
        $delete_count = count($delete_ids);
        $create_count = count($create_ids);
        $min_count = min($delete_count, $create_count);

        if ($create_count > $delete_count) {
            //新增多出
            for ($i = $delete_count; $i < $create_count; ++$i) {
                // var_dump('新增多出grant_api_id='.$create_ids[$i]);
                PermissionGrantApiModel::create(['permission_id' => $id, 'grant_api_id' => $create_ids[$i]]);
            }
        } elseif ($create_count < $delete_count) {
            // 删除以前记录中多余的
            $should_delete_ids = array_slice($delete_ids, $min_count, ($delete_count - $create_count));
            PermissionGrantApiModel::where('permission_id', $id)->whereIn('grant_api_id', $should_delete_ids)->delete();
        } else {
        }
        // 按顺序修改原来的关系为新的值
        for ($i = 0; $i < $min_count; ++$i) {
            PermissionGrantApiModel::where(['permission_id' => $id, 'grant_api_id' => $delete_ids[$i]])->update(['grant_api_id' => $create_ids[$i]]);
        }

        // // TODO: 新增和移除优化
        // PermissionGrantApiModel::where('permission_id', $id)->delete();

        // $data['permission_id'] = $id;
        // foreach ($api_ids as $api_id) {
        //     $data['grant_api_id'] = $api_id;
        //     $model = PermissionGrantApiModel::where($data)->first();
        //     if (!$model) { //防止重复添加
        //         PermissionGrantApiModel::create($data);
        //     }
        // }

        return true;
    }
}
