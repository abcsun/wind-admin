<?php

namespace Wind\Repositories;

use DB;
use Wind\Models\UserModel;
use Wind\Models\UserRoleModel;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * 用户相关数据模型仓库.
 *
 * @author scl <scl@winhu.com>
 */
class UserRepository extends AbstractRepository
{
    /**
     * 判断用户是否可以访问给定的api.
     *
     * @param [type] $user_id  [description]
     * @param [type] $api_slug [description]
     *
     * @return [type] [description]
     */
    public function assertAccess($user_id, $api_slug)
    {
        $result = UserModel::leftJoin('user_role', function ($join) use ($user_id) {
                        $join->on('user.id', '=', 'user_role.user_id')
                            ->whereNull('user_role.deleted_at')
                            ->where('user.id', '=', $user_id);
                    })
                    ->leftJoin('role_permission', function ($join) {
                        $join->on('user_role.role_id', '=', 'role_permission.role_id')
                            ->whereNull('role_permission.deleted_at');
                    })
                    ->leftJoin('permission_grant_api', function ($join) {
                        $join->on('role_permission.permission_id', '=', 'permission_grant_api.permission_id')
                            ->whereNull('permission_grant_api.deleted_at');
                    })
                    ->leftJoin('grant_api', function ($join) use ($api_slug) {
                        $join->on('permission_grant_api.grant_api_id', '=', 'grant_api.id')
                            ->whereNull('grant_api.deleted_at')
                            ->where('grant_api.slug', '=', $api_slug);
                    })
                    ->whereNotNull('user_role.role_id')
                    ->whereNotNull('role_permission.permission_id')
                    ->whereNotNull('permission_grant_api.id')
                    ->whereNotNull('grant_api.id')
                    ->select('grant_api.id as api_id', 'user.id as user_id')
                    ->first();

        if (!$result) {
            throw new AccessDeniedHttpException('未授权的API访问');
        }

        return $result;
    }

    /**
     * 增加用户角色.
     *
     * @param [type] $id       [description]
     * @param array  $role_ids [description]
     */
    public function addRoles($id, array $role_ids)
    {
        $current_ids = UserRoleModel::where('user_id', $id)->lists('role_id')->toArray();
        $delete_ids = array_values(array_diff($current_ids, $role_ids));
        $create_ids = array_values(array_diff($role_ids, $current_ids));
        $delete_count = count($delete_ids);
        $create_count = count($create_ids);
        $min_count = min($delete_count, $create_count);

        if ($create_count > $delete_count) {
            //新增多出
            for ($i = $delete_count; $i < $create_count; ++$i) {
                // var_dump('新增多出role_id='.$create_ids[$i]);
                UserRoleModel::create(['user_id' => $id, 'role_id' => $create_ids[$i]]);
            }
        } elseif ($create_count < $delete_count) {
            // 删除以前记录中多余的
            $should_delete_ids = array_slice($delete_ids, $min_count, ($delete_count - $create_count));
            UserRoleModel::where('user_id', $id)->whereIn('role_id', $should_delete_ids)->delete();
        } else {
        }
        // 按顺序修改原来的关系为新的值
        for ($i = 0; $i < $min_count; ++$i) {
            UserRoleModel::where(['user_id' => $id, 'role_id' => $delete_ids[$i]])->update(['role_id' => $create_ids[$i]]);
        }

        // TODO: 新增和移除优化
        // UserRoleModel::where('user_id', $id)->delete();

        // $data['user_id'] = $id;
        // foreach ($role_ids as $role_id) {
        //     $data['role_id'] = $role_id;
        //     $model = UserRoleModel::where($data)->first();
        //     if (!$model) { //防止重复添加
        //         UserRoleModel::create($data);
        //     }
        // }

        return true;
    }

    /**
     * 检测field中新的value是否重复注册
     * 可以为phone/email.
     *
     * @param [type] $field [description]
     * @param [type] $value [description]
     *
     * @return [type] [description]
     */
    public function canRegisterBy($field, $value)
    {
        return UserModel::where($field, $value)->first();
    }

    /**
     * 使用field域登录验证
     *
     * @param [type] $field    [description]
     * @param [type] $value    [description]
     * @param [type] $password [description]
     *
     * @return [type] [description]
     */
    public function authUserBy($field, $value, $password)
    {
        $user = UserModel::where($field, $value)->first();
        if (!$user || !password_verify($password, $user->password)) {
            return false;
        }

        return $user;
    }

    /**
     * 根据用户权限生成动态菜单.
     *
     * @param [type] $user_id [description]
     *
     * @return [type] [description]
     */
    public function generateMenuByUser($user_id)
    {
        $prefix = env('DB_PREFIX', '');
        $permission_table = $prefix.'permission';
        $menus = UserRoleModel::leftJoin('role_permission', function ($join) use ($user_id) {
                        $join->on('user_role.role_id', '=', 'role_permission.role_id')
                            ->where('user_role.user_id', '=', $user_id)
                            ->whereNull('role_permission.deleted_at');
                    })
                    ->leftJoin('permission', function ($join) {
                        $join->on('role_permission.permission_id', '=', 'permission.id')
                            ->whereNull('permission.deleted_at');
                    })

                    ->whereNotNull('user_role.role_id')
                    ->whereNotNull('role_permission.permission_id')
                    ->whereNotNull('permission.id')

                    ->selectRaw(DB::raw("distinct($permission_table.id), $permission_table.slug, $permission_table.url, $permission_table.name, $permission_table.pid, $permission_table.type, $permission_table.display_name, $permission_table.description"))
                    ->orderBy('permission.sort', 'asc')
                    ->get();

        return $menus;
    }
}
