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
        Schema::create('cards', function (Blueprint $table) {
            $table->comment('兑换卡密');
            $table->id();
            $table->boolean('status')->default(true)->comment('状态');
            $table->string('secret')->unique()->comment('卡密');
            $table->unsignedInteger('vip_id')->comment('会员套餐');
            $table->unsignedInteger('month')->comment('有效期/月');
            $table->boolean('used')->default(false)->comment('是否已使用');
            $table->unsignedBigInteger('user_id')->nullable()->comment('使用人ID');
            $table->timestamp('exchange_time')->nullable()->comment('兑换时间');
            $table->timestamp('expiry_time')->comment('到效期时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
