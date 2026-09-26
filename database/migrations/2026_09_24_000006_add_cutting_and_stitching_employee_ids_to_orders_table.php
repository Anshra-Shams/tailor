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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('cutting_employee_id')->nullable()->after('assigned_employee_id')->constrained('employees')->onDelete('set null');
            $table->foreignId('stitching_employee_id')->nullable()->after('cutting_employee_id')->constrained('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cutting_employee_id']);
            $table->dropForeign(['stitching_employee_id']);
            $table->dropColumn(['cutting_employee_id', 'stitching_employee_id']);
        });
    }
};
