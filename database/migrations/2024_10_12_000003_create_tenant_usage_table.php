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
        Schema::create('tenant_usage', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->integer('product_count')->default(0);
            $table->integer('user_count')->default(0);
            $table->bigInteger('storage_used')->default(0); // in bytes
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_usage');
    }
};
