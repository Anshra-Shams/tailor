<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('website_link')->nullable();
            $table->text('invoice_terms')->nullable();
            $table->timestamps();
        });

        // Insert initial default settings so existing invoice data remains preserved
        DB::table('settings')->insert([
            'company_name'  => 'Meco',
            'company_logo'  => null,
            'phone'         => '0223-860680 - 0223862252',
            'address'       => 'SHOP # 5 & 6 RED CRESCENT HOSPITAL MARKET LATIFABAD # 6, HYDERABAD',
            'website_link'  => 'www.Meco.Com.Pk',
            'invoice_terms' => "Due Electricity failure delivery can be delay\nAfter one month of Deliver date we wouldn't accept any claim",
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
