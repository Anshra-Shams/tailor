<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make service_id nullable so general measurements can be stored without a specific service
        DB::statement('ALTER TABLE measurements MODIFY service_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE measurements MODIFY service_id BIGINT UNSIGNED NOT NULL');
    }
};
