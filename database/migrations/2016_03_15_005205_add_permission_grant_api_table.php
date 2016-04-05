<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionGrantApiTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('permission_grant_api', function (Blueprint $table) {
            $table->increments('id');

            $table->Integer('permission_id')->comment('权限id');
            $table->Integer('grant_api_id')->comment('授权API id');

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
        Schema::drop('permission_grant_api');
    }
}
