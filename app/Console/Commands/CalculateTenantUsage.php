<?php

namespace App\Console\Commands;

use App\Tenant;
use Illuminate\Console\Command;

class CalculateTenantUsage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:calculate-usage {tenant_id?}';

    /**
     * The console command description.
     */
    protected $description = 'Calculate usage statistics for tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant {$tenantId} not found");
                return self::FAILURE;
            }
            $this->calculateForTenant($tenant);
        } else {
            $tenants = Tenant::all();
            $bar = $this->output->createProgressBar($tenants->count());
            $bar->start();

            foreach ($tenants as $tenant) {
                $this->calculateForTenant($tenant);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        $this->info('Usage calculation completed!');
        return self::SUCCESS;
    }

    /**
     * Calculate usage for a single tenant.
     */
    protected function calculateForTenant(Tenant $tenant): void
    {
        $usage = $tenant->usage;
        if (!$usage) {
            $usage = $tenant->usage()->create([
                'product_count' => 0,
                'user_count' => 0,
                'storage_used' => 0,
            ]);
        }

        $usage->calculate();
    }
}
