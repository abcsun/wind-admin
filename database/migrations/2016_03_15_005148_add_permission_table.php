<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('permission', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('pid')->comment('上级id');

            $table->string('name')->comment('权限名称');
            $table->string('url')->comment('前端路由');
            $table->string('slug')->comment('前端别名');
            $table->string('display_name')->comment('显示名称');
            $table->string('description')->comment('简介');
            $table->tinyInteger('type')->default(1)->comment('权限类别：1=>菜单, 2=>节点, 3=>页面, 4=>功能');
            
            $table->tinyInteger('sort')->default(100)->comment('排序');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->tinyInteger('x_status')->default(1)->comment('启停标志,0-禁用');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('permission');
    }
}
