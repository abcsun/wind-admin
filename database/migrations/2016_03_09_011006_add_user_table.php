<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');

            $table->string('open_id', 16)->comment('用户账号为你id');  //注册手机号，唯一
            $table->string('phone', 11)->unique()->comment('注册手机号');  //注册手机号，唯一
            $table->string('password')->comment('密码');        //密码
            $table->string('name')->comment('用户昵称');               //用户昵称
            $table->string('gravatar')->comment('头像');           //头像
            $table->string('role', 16)->default('user')->comment('表征用户当前最高角色user/admin，默认为user');
            
            $table->boolean('is_teacher')->comment('教师标记');        //教师标记
            $table->tinyInteger('x_status')->default(1)->comment('启停标志,0-禁用');
            $table->float('account_remain')->comment('账户余额');   //账户余额

            $table->timestamps();
            $table->softDeletes();  //add softdelete fields

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('user');
    }
}
