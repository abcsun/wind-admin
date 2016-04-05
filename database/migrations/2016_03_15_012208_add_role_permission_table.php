<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('role_permission', function (Blueprint $table) {
            $table->increments('id');

            $table->Integer('role_id')->comment('角色id');
            $table->Integer('permission_id')->comment('权限id');

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
        Schema::drop('role_permission');
    }
}
