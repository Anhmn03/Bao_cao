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
        Schema::create('cacu_salaries', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->integer('valid_workdays');
        $table->integer('invalid_workdays');
        $table->decimal('salary_amount', 15, 2)->nullable();
        $table->date('month');
        $table->timestamps();
        $table->integer('created_by')->nullable();  // Để không gặp lỗi nếu không có giá trị
        $table->integer('updated_by')->nullable();  // Tương tự
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cacu_salaries');

    }
};
