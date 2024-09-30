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
        Schema::create('vip_logs', function (Blueprint $table) {
            $table->comment('VIP购买记录表');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('会员id');
            $table->unsignedBigInteger('payment_id')->nullable()->comment('支付ID');
            $table->unsignedBigInteger('vip_id')->nullable()->comment('会员ID');
            $table->unsignedTinyInteger('status')->comment('状态');
            $table->timestamp('start_at')->comment('开始时间');
            $table->timestamp('end_at')->comment('结束时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_logs');
    }
};
