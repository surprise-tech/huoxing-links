<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mini_programs', function (Blueprint $table) {
            $table->comment('小程序配置');
            $table->id();
            $table->string('name')->comment('小程序名称');
            $table->string('app_id')->comment('app_id');
            $table->string('secret')->comment('secret');
            $table->string('url')->comment('首页');
            $table->unsignedBigInteger('type')->comment('小程序类型');
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->boolean('is_pre_min')->default(false)->comment('是否为平台小程序池小程序');
            $table->boolean('is_enable')->default(true)->comment('是否启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mini_programs');
    }
};
