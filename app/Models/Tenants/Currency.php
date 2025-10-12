<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperCurrency
 */
class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'decimal_places' => 'integer',
    ];

    /**
     * Scope a query to only include active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default currency.
     */
    public static function getDefault(): ?Currency
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Set this currency as the default currency.
     */
    public function setAsDefault(): void
    {
        // Unset all other currencies as default
        static::where('id', '!=', $this->id)->update(['is_default' => false]);
        
        // Set this currency as default
        $this->update(['is_default' => true]);
    }

    /**
     * Format an amount with this currency.
     */
    public function format(float $amount): string
    {
        $formatted = number_format($amount, $this->decimal_places);
        
        if ($this->symbol_position === 'before') {
            return $this->symbol . ' ' . $formatted;
        }
        
        return $formatted . ' ' . $this->symbol;
    }
}
