<?php

namespace App\Observers;

use App\Models\Tenants\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $this->updateUsageCount();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->updateUsageCount();
    }

    /**
     * Update tenant usage count.
     */
    protected function updateUsageCount(): void
    {
        $tenant = tenancy()->tenant;
        
        if (!$tenant || !$tenant->usage) {
            return;
        }

        $tenant->usage->update([
            'user_count' => User::count(),
            'last_calculated_at' => now(),
        ]);
    }
}
