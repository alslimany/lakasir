<x-filament-panels::page>
    @php
        $tenant = tenancy()->tenant;
        $currentPlan = $this->getCurrentPlan();
        $trialInfo = $this->getTrialInfo();
        $usageInfo = $this->getUsageInfo();
        $availablePlans = $this->getAvailablePlans();
    @endphp

    <div class="space-y-6">
        {{-- Trial Banner --}}
        @if($trialInfo)
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Trial Period Active</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Your trial ends {{ $trialInfo['ends_at']->diffForHumans() }} 
                                ({{ abs($trialInfo['days_left']) }} days remaining)
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Current Plan --}}
        @if($currentPlan)
            <x-filament::section>
                <x-slot name="heading">
                    Current Plan
                </x-slot>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">{{ $currentPlan->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $currentPlan->description }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold">${{ number_format($currentPlan->price, 2) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">per {{ $currentPlan->interval }}</p>
                        </div>
                    </div>

                    @if($currentPlan->features)
                        <div class="border-t dark:border-gray-700 pt-4">
                            <h4 class="font-semibold mb-2">Plan Features</h4>
                            <ul class="space-y-2">
                                @foreach($currentPlan->features as $feature => $value)
                                    <li class="flex items-center text-sm">
                                        <svg class="h-5 w-5 text-success-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="capitalize">{{ str_replace('_', ' ', $feature) }}: 
                                            @if($value === -1)
                                                Unlimited
                                            @else
                                                {{ is_numeric($value) ? number_format($value) : $value }}
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        @endif

        {{-- Usage Information --}}
        @if($usageInfo)
            <x-filament::section>
                <x-slot name="heading">
                    Current Usage
                </x-slot>

                <div class="space-y-6">
                    {{-- Products Usage --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium">Products</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $usageInfo['products']['current'] }} 
                                @if(!$usageInfo['products']['unlimited'])
                                    / {{ number_format($usageInfo['products']['limit']) }}
                                @else
                                    (Unlimited)
                                @endif
                            </span>
                        </div>
                        @if(!$usageInfo['products']['unlimited'])
                            @php
                                $percentage = $usageInfo['products']['limit'] > 0 
                                    ? ($usageInfo['products']['current'] / $usageInfo['products']['limit']) * 100 
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    {{-- Users Usage --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium">Users</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $usageInfo['users']['current'] }} 
                                @if(!$usageInfo['users']['unlimited'])
                                    / {{ number_format($usageInfo['users']['limit']) }}
                                @else
                                    (Unlimited)
                                @endif
                            </span>
                        </div>
                        @if(!$usageInfo['users']['unlimited'])
                            @php
                                $percentage = $usageInfo['users']['limit'] > 0 
                                    ? ($usageInfo['users']['current'] / $usageInfo['users']['limit']) * 100 
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        @endif
                    </div>

                    {{-- Storage Usage --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium">Storage</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $usageInfo['storage']['current'] }}</span>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Available Plans --}}
        <x-filament::section>
            <x-slot name="heading">
                Available Plans
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($availablePlans as $plan)
                    <div class="border rounded-lg p-6 {{ $currentPlan?->id === $plan->id ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                        </div>

                        <div class="text-center mb-6">
                            <p class="text-3xl font-bold">${{ number_format($plan->price, 2) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">per {{ $plan->interval }}</p>
                        </div>

                        <ul class="space-y-2 mb-6">
                            @foreach($plan->features as $feature => $value)
                                <li class="flex items-center text-sm">
                                    <svg class="h-4 w-4 text-success-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="capitalize">{{ str_replace('_', ' ', $feature) }}: 
                                        @if($value === -1)
                                            Unlimited
                                        @else
                                            {{ is_numeric($value) ? number_format($value) : $value }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        @if($currentPlan?->id === $plan->id)
                            <button disabled class="w-full py-2 px-4 bg-gray-300 text-gray-600 rounded-lg cursor-not-allowed">
                                Current Plan
                            </button>
                        @else
                            <button 
                                wire:click="$dispatch('open-modal', { id: 'choose-plan-modal' })"
                                class="w-full py-2 px-4 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition">
                                Choose Plan
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
