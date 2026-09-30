<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->date('from_date')->nullable()->after('employee_id');
            $table->date('to_date')->nullable()->after('from_date');
            $table->string('salary_month', 7)->nullable()->change();
            $table->string('payment_method')->nullable()->default('cash')->change();
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['from_date', 'to_date']);
            $table->string('salary_month', 7)->nullable(false)->change();
        });
    }
};
