<?php

use Illuminate\Database\Seeder;

//api table
class GrantApiTableSeeder extends Seeder {
    public function run()
    {
        DB::table('grant_api')->truncate();
        // config
        DB::table('grant_api')->insert(array(
            'slug' => 'config.index',
            'name' => '配置列表',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.store',
            'name' => '新增配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.show',
            'name' => '获取配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.update',
            'name' => '修改配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.delete',
            'name' => '删除配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.batch',
            'name' => '批量操作配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.batch.update',
            'name' => '批量修改配置项',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'config.batch.delete',
            'name' => '批量删除配置项',
        ));
        // permission
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.index',
            'name' => '权限列表',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.store',
            'name' => '新增权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.update',
            'name' => '修改权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.show',
            'name' => '获取权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.delete',
            'name' => '删除权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.add.api',
            'name' => '权限增加API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.batch',
            'name' => '批量操作权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.batch.update',
            'name' => '批量修改权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'permission.batch.delete',
            'name' => '批量删除权限',
        ));

        // account
        DB::table('grant_api')->insert(array(
            'slug' => 'account.register',
            'name' => '用户注册',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'account.login',
            'name' => '用户登录',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'account.logout',
            'name' => '用户退出',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'account.probe_login',
            'name' => '用户登录状态监测',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'account.password.forget',
            'name' => '忘记密码',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'account.password.reset',
            'name' => '重置密码',
        ));

        // user
        DB::table('grant_api')->insert(array(
            'slug' => 'user.index',
            'name' => '用户列表',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.store',
            'name' => '新增用户',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.update',
            'name' => '修改用户',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.show',
            'name' => '获取用户',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.delete',
            'name' => '删除用户',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.add.role',
            'name' => '用户增加角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.batch.update',
            'name' => '批量修改用户',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'user.batch.delete',
            'name' => '批量删除用户',
        ));

        // role
        DB::table('grant_api')->insert(array(
            'slug' => 'role.index',
            'name' => '角色列表',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.store',
            'name' => '新增角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.update',
            'name' => '修改角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.show',
            'name' => '获取角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.delete',
            'name' => '删除角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.add.permission',
            'name' => '角色增加权限',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.batch.update',
            'name' => '批量修改角色',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'role.batch.delete',
            'name' => '批量删除角色',
        ));

        // api
        DB::table('grant_api')->insert(array(
            'slug' => 'api.index',
            'name' => 'API列表',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.store',
            'name' => '新增API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.update',
            'name' => '修改API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.show',
            'name' => '获取API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.delete',
            'name' => '删除API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.batch.update',
            'name' => '批量修改API',
        ));
        DB::table('grant_api')->insert(array(
            'slug' => 'api.batch.delete',
            'name' => '批量删除API',
        ));
    }
}
