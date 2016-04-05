<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->comment('用户id');          //用户id

            $table->string('email')->comment('邮箱');              //邮箱
            $table->boolean('gender')->comment('性别');            //性别
            $table->date('birthday')->comment('生日');             //生日

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
        Schema::drop('user_profile');
    }
}
