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
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255); // Tên phòng ban
            $table->integer('parent_id')->default(0); // ID của phòng ban cha, mặc định là 0
            $table->integer('status')->default(1); // Trạng thái phòng ban, mặc định là 1 (kích hoạt)
            $table->integer('created_at'); // Thời gian tạo
            $table->integer('created_by'); // Người tạo
            $table->integer('updated_at'); // Thời gian cập nhật
            $table->integer('updated_by'); // Người cập nhật
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');

    }
};
