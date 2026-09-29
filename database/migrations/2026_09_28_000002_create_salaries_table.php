<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('salary_month', 7); // Format: YYYY-MM (e.g. 2026-09)
            $table->date('payment_date');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash, bank_transfer, online, cheque
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('status')->default('paid'); // paid, partial, pending
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'salary_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
