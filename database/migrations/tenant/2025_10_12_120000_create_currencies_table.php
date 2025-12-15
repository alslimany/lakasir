<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\TenantDefaults;
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
        $currentCurrency = Schema::hasTable('settings')
            ? DB::table('settings')->where('key', 'currency')->value('value') ?? TenantDefaults::CURRENCY
            : TenantDefaults::CURRENCY;
        
        $currencies = [
            [
                'code' => 'LYD',
                'name' => 'Libyan Dinar',
                'symbol' => 'LYD',
                'symbol_position' => 'before',
                'decimal_places' => 3,
                'is_active' => true,
                'is_default' => $currentCurrency === 'LYD',
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
        ];

        if (collect($currencies)->where('is_default', true)->isEmpty()) {
            $currencies[0]['is_default'] = true;
        }

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
