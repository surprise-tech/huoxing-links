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
        Schema::create('vip_packages', function (Blueprint $table) {
            $table->comment('vip套餐配置');
            $table->id();
            $table->string('name')->comment('套餐名称');
            $table->unsignedBigInteger('price')->comment('价格/月');
            $table->unsignedTinyInteger('level')->comment('等级');
            $table->json('config')->comment('权益配置');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vip_packages');
    }
};
