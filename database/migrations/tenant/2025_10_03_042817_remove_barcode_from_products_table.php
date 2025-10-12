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
        // Check if the barcode column exists before trying to migrate data
        if (Schema::hasColumn('products', 'barcode')) {
            $products = DB::table('products')->whereNotNull('barcode')->get();

            foreach ($products as $product) {
                // Check if barcode already exists to avoid duplicates
                $exists = DB::table('barcodes')
                    ->where('product_id', $product->id)
                    ->where('code', $product->barcode)
                    ->exists();
                
                if (!$exists) {
                    DB::table('barcodes')->insert([
                        'product_id' => $product->id,
                        'code' => $product->barcode,
                        'type' => 'primary',
                        'description' => __('Migrated from original barcode'),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('barcode');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku');
        });

        $barcodes = DB::table('barcodes')
            ->where('type', 'primary')
            ->where('is_active', true)
            ->get();

        foreach ($barcodes as $barcode) {
            DB::table('products')
                ->where('id', $barcode->product_id)
                ->update(['barcode' => $barcode->code]);
        }
    }
};
