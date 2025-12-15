<?php

namespace App\Policies;

use App\Models\Admin;
use App\TenantUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenantUserPolicy
{
    use HandlesAuthorization;

    public function viewAny(Admin $user): bool
    {
        return true;
    }

    public function view(Admin $user, TenantUser $tenantUser): bool
    {
        return true;
    }

    public function create(Admin $user): bool
    {
        return true;
    }

    public function update(Admin $user, TenantUser $tenantUser): bool
    {
        return true;
    }

    public function delete(Admin $user, TenantUser $tenantUser): bool
    {
        return true;
    }

    public function restore(Admin $user, TenantUser $tenantUser): bool
    {
        return true;
    }

    public function forceDelete(Admin $user, TenantUser $tenantUser): bool
    {
        return true;
    }
}
