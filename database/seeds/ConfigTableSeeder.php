<?php

use Illuminate\Database\Seeder;

/**
 * config 表数据填充
 * Date: 16/3/18
 * Author: eric <eric@winhu.com>
 */
class ConfigTableSeeder extends Seeder
{
    public function run()
    {
        // TODO: Implement run() method.
        DB::table('config')->truncate();
        
        DB::table('config')->insert(array(
            'name'  => 'WEB_SITE_TITLE',
            'title' => '网站标题',
            'group' => 1,
            'type'  => 1,
            'sort'  => 10,
            'value' => '学天下',
            'remark'=> '网站标题前台显示标题',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'CONFIG_TYPE_LIST',
            'title' => '配置类型列表',
            'group' => 5,
            'type'  => 3,
            'sort'  => 3,
            'value' => '0:数字
                        1:字符
                        2:文本
                        3:数组
                        4:枚举',
            'remark'=> '主要用于数据解析和页面表单的生成',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'WECHAT_APP_ID',
            'title' => 'app_id',
            'group' => 2,
            'type'  => 1,
            'sort'  => 0,
            'value' => 'wx9616c012dd2f3848',
            'remark'=> '',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'LIMIT',
            'title' => '调用条数',
            'group' => 3,
            'type'  => 3,
            'sort'  => 0,
            'value' => 'article:12
                        images:10
                        download:10
                        product:12',
            'remark'=> '',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'ALLOW_VISIT',
            'title' => '不受限控制器方法',
            'group' => 4,
            'type'  => 3,
            'sort'  => 0,
            'value' => '0:article/draftbox
                        1:article/mydocument
                        2:Category/tree
                        3:Index/verify
                        4:file/upload
                        5:file/download
                        6:user/updatePassword
                        7:user/updateNickname
                        8:user/submitPassword
                        9:user/submitNickname
                        10:file/uploadpicture
                        11:Addons/execute
                        12:ItemProperty/getprop',
            'remark'=> '',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'URL',
            'title' => '前台地址',
            'group' => 5,
            'type'  => 1,
            'sort'  => 0,
            'value' => 'http://xuetianxia.com/',
            'remark'=> '',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'DEBUG_SQL',
            'title' => 'SQL记录',
            'group' => 5,
            'type'  => 1,
            'sort'  => 0,
            'value' => 1,
            'remark'=> '',
            'x_status'=>1
        ));
        DB::table('config')->insert(array(
            'name'  => 'API_ACL',
            'title' => 'API访问控制开关',
            'group' => 5,
            'type'  => 1,
            'sort'  => 0,
            'value' => 1,
            'remark'=> '',
            'x_status'=>1
        ));

    }
}