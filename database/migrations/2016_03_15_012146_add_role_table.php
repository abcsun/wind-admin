<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('角色名称');
            $table->tinyInteger('type')->default(2)->comment('角色类型；1-后台管理员；2-普通用户');
            $table->string('description')->after('type')->comment('角色描述');

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
        Schema::drop('role');
    }
}
