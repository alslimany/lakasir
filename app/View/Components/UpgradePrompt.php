<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UpgradePrompt extends Component
{
    public $feature;
    public $currentCount;
    public $limit;
    public $featureName;

    /**
     * Create a new component instance.
     */
    public function __construct(string $feature)
    {
        $this->feature = $feature;
        $tenant = tenancy()->tenant;

        if ($tenant) {
            $plan = $tenant->subscriptionPlan;
            $usage = $tenant->usage;

            $this->limit = $plan?->getFeatureLimit($feature);
            $this->currentCount = match($feature) {
                'products' => $usage?->product_count ?? 0,
                'users' => $usage?->user_count ?? 0,
                default => 0,
            };
            
            $this->featureName = match($feature) {
                'products' => 'Products',
                'users' => 'Users',
                default => ucfirst($feature),
            };
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.upgrade-prompt');
    }

    /**
     * Check if upgrade is needed.
     */
    public function needsUpgrade(): bool
    {
        if (!$this->limit || $this->limit === -1) {
            return false;
        }

        return $this->currentCount >= $this->limit;
    }

    /**
     * Get remaining count.
     */
    public function remaining(): int
    {
        if (!$this->limit || $this->limit === -1) {
            return -1; // Unlimited
        }

        return max(0, $this->limit - $this->currentCount);
    }

    /**
     * Get usage percentage.
     */
    public function percentage(): int
    {
        if (!$this->limit || $this->limit === -1) {
            return 0;
        }

        return min(100, (int)(($this->currentCount / $this->limit) * 100));
    }
}
