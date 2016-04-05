<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id');

            $table->tinyInteger('type')->comment('配置类型');
            $table->tinyInteger('group')->comment('分组');
            $table->tinyInteger('sort')->comment('排序');
            $table->string('name')->comment('配置名称');
            $table->string('title')->comment('配置项');
            $table->text('value')->comment('配置值');
            $table->string('remark')->comment('配置说明');

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
        Schema::drop('config');
    }
}
