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
        Schema::create('salaries', function (Blueprint $table) {
            $table->bigIncrements('id'); // ID tự động tăng
            $table->string('name'); // Tên (có thể thêm độ dài nếu cần, ví dụ: string('name', 50))
            $table->unsignedBigInteger('department_id'); // ID phòng ban (kiểu INT 10, unsigned)
            $table->decimal('salaryCoefficient', 5, 2); // Hệ số lương, độ chính xác 5,2
            $table->decimal('monthlySalary', 10, 2); // Lương tháng, độ chính xác 10,2
            $table->integer('created_at');  // Thời gian tạo (UNIX timestamp)
            $table->integer('created_by');  // ID người tạo bản ghi
            $table->integer('updated_at');  // Thời gian cập nhật (UNIX timestamp)
            $table->integer('updated_by');  // ID người cập nhật bản ghi
          

            // Thiết lập khóa ngoại cho department_id
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
