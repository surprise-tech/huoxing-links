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
        Schema::create('commission_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->default(0)->index()->comment('类型');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('children_user_id')->nullable();
            $table->string('amount');
            $table->string('title');
            $table->boolean('is_withdraw')->default(false)->comment('是否提现');
            $table->json('payee')->nullable()->comment('收款人信息');
            $table->unsignedTinyInteger('status')->comment('状态');
            $table->timestamps();
            $table->comment('佣金记录');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_logs');
    }
};
