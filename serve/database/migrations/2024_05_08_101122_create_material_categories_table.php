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
        Schema::create('material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->unsignedInteger('sort')->default(0)->comment('权重');
            $table->tinyInteger('type')->default(0)->comment('类型1系统内置');
            $table->timestamps();
            $table->comment('素材库分类');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_categories');
    }
};
