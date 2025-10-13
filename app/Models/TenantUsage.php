<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantUsage extends Model
{
    use HasFactory;

    protected $table = 'tenant_usage';

    protected $fillable = [
        'tenant_id',
        'product_count',
        'user_count',
        'storage_used',
        'last_calculated_at',
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the usage.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Tenant::class);
    }

    /**
     * Get formatted storage size.
     */
    public function getFormattedStorageAttribute(): string
    {
        $bytes = $this->storage_used;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Calculate and update current usage.
     */
    public function calculate(): void
    {
        $tenant = $this->tenant;
        
        $tenant->run(function () {
            $this->product_count = \App\Models\Tenants\Product::count();
            $this->user_count = \App\Models\Tenants\User::count();
            
            // Calculate storage used from uploaded files
            $this->storage_used = \App\Models\Tenants\UploadedFile::sum('size') ?? 0;
            
            $this->last_calculated_at = now();
            $this->save();
        });
    }
}
