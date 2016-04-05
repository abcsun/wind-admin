<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGrantApiTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('grant_api', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->comment('路由名称');
            $table->string('slug')->comment('API别名');
            $table->string('path')->comment('API路径');
            $table->tinyInteger('method')->default(1)->comment('http请求方式：1-GET，2-POST，3-PUT，4-DELETE');

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
        Schema::drop('grant_api');
    }
}
