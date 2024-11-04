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
        Schema::create('salary_level', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->decimal('mothly_salary', 15, 2)->nullable();
            $table->decimal('daily_salary', 15, 2)->nullable();
            $table->integer('created_at');  // Thời gian tạo (UNIX timestamp)
            $table->integer('created_by');  // ID người tạo bản ghi
            $table->integer('updated_at');  // Thời gian cập nhật (UNIX timestamp)
            $table->integer('updated_by');  // ID người cập nhật bản ghi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_levels');

    }
};
