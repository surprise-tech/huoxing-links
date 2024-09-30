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
        Schema::create('links', function (Blueprint $table) {
            $table->comment('链接表');
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('创建人id');
            $table->string('title')->comment('标题');
            $table->unsignedTinyInteger('type')->comment('类型');
            $table->unsignedTinyInteger('status')->default(0)->comment('url状态');
            $table->string('icon')->comment('缩略图');
            $table->string('description')->nullable()->comment('描述');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('code')->unique()->comment('短链接代码');
            $table->json('config')->comment('链接配置');
            $table->unsignedBigInteger('price')->default(0)->comment('价格');
            $table->timestamp('expired_at')->nullable()->comment('有效期');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
