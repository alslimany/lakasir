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
        Schema::table('tenants', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('data');
            $table->timestamp('trial_ends_at')->nullable()->after('is_active');
            $table->foreignId('subscription_plan_id')->nullable()->after('trial_ends_at')->constrained('subscription_plans')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn(['is_active', 'trial_ends_at', 'subscription_plan_id']);
        });
    }
};
