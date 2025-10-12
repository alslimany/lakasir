<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // ISO 4217 code (e.g., USD, IDR)
            $table->string('name'); // Full name (e.g., US Dollar, Indonesian Rupiah)
            $table->string('symbol', 10); // Currency symbol (e.g., $, Rp)
            $table->string('symbol_position')->default('before'); // before or after
            $table->integer('decimal_places')->default(2); // Number of decimal places
            $table->boolean('is_active')->default(true); // Is currency active
            $table->boolean('is_default')->default(false); // Is this the default currency
            $table->timestamps();
        });

        // Insert default currencies
        $currentCurrency = DB::table('settings')->where('key', 'currency')->value('value') ?? 'IDR';
        
        $currencies = [
            [
                'code' => 'IDR',
                'name' => 'Indonesian Rupiah',
                'symbol' => 'Rp',
                'symbol_position' => 'before',
                'decimal_places' => 0,
                'is_active' => true,
                'is_default' => $currentCurrency === 'IDR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MXN',
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'MXN',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'EUR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'GBP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'symbol' => 'ر.س',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'SAR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'symbol' => 'د.إ',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'is_active' => true,
                'is_default' => $currentCurrency === 'AED',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
