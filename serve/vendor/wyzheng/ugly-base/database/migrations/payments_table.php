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
        Schema::create('payments', function (Blueprint $table) {
            $table->comment('支付记录表');
            $table->id();
            $table->string('no')->unique()->comment('支付编号');
            $table->string('order_no')->nullable()->comment('内部订单号');
            $table->string('channel')->comment('支付渠道');
            $table->unsignedBigInteger('amount')->comment('支付金额/分');
            $table->unsignedTinyInteger('type')->index()->comment('支付类型: 1付款 2退款 3转账');
            $table->unsignedTinyInteger('status')->index()->comment('状态: 1处理中 2成功 3失败');
            $table->timestamp('success_at')->nullable()->comment('成功时间');
            $table->timestamp('fail_at')->nullable()->comment('失败时间');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('notification_no')->nullable()->comment('第三方支付通知单号');
            $table->json('notification_data')->nullable()->comment('第三方支付通知原始数据');
            $table->string('job')->nullable()->comment('回调任务');
            $table->json('attach')->nullable()->comment('附加信息');
            $table->unsignedBigInteger('payment_id')->nullable()->comment('退款时关联支付单ID');
            $table->nullableMorphs('merchant');
            $table->nullableMorphs('payer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
