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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->comment('账号');
            $table->string('password')->comment('密码');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态');
            $table->unsignedBigInteger('type')->comment('账号类型');
            $table->unsignedBigInteger('credit')->default(0)->comment('账户剩余积分');
            $table->unsignedBigInteger('accumulate_credit')->default(0)->comment('累计充值积分');
            $table->unsignedBigInteger('commission')->default(0)->comment('未提现佣金');
            $table->unsignedBigInteger('accumulate_commission')->default(0)->comment('累计佣金');
            $table->unsignedBigInteger('vip_id')->nullable()->comment('vip套餐ID');
            $table->unsignedBigInteger('agent_id')->nullable()->comment('代理套餐ID');
            $table->unsignedBigInteger('level_id')->nullable()->comment('代理级别ID');
            $table->timestamp('start_at')->nullable()->comment('套餐生效时间');
            $table->timestamp('end_at')->nullable()->comment('套餐过期时间');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('上级ID');
            $table->string('referral_code')->unique()->comment('推荐码');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
