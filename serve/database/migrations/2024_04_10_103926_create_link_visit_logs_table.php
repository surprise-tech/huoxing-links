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
        Schema::create('link_visit_logs', function (Blueprint $table) {
            $table->comment('链接访问记录表');
            $table->id();
            $table->unsignedBigInteger('link_id')->comment('卡片链接id');
            $table->unsignedBigInteger('user_id')->comment('卡片链接所属用户ID');
            $table->string('ip')->nullable()->comment('访问IP');
            $table->string('device_uid')->nullable()->comment('设备唯一标识');
            $table->json('cache')->nullable()->comment('缓存链接数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_visit_logs');
    }
};
