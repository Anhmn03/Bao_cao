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
        Schema::create('user_attendance', function (Blueprint $table) {
            $table->id();  // Tự động tạo khóa chính với AUTO_INCREMENT
            $table->unsignedBigInteger('user_id');  // Khóa ngoại liên kết với bảng user
            $table->integer('time');  // Thời gian (UNIX timestamp)
            $table->string('type', 20);  // Loại hành động ("in" hoặc "out")
            $table->boolean('valid_status')->default(true)->change(); // true = Hợp lệ, false = Không hợp lệ
            $table->text('justification')->nullable(); // Lý do giải trình
            $table->integer('created_at');  // Thời gian tạo (UNIX timestamp)
            $table->integer('created_by');  // ID người tạo bản ghi
            $table->integer('updated_at');  // Thời gian cập nhật (UNIX timestamp)
            $table->integer('updated_by');  // ID người cập nhật bản ghi
          
            // Khóa ngoại liên kết với bảng user
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_attendance');

    }
};
