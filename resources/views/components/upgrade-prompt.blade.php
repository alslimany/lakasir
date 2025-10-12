@if($needsUpgrade())
    <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg p-4 mb-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-semibold text-warning-800 dark:text-warning-200">
                    {{ $featureName }} Limit Reached
                </h3>
                <p class="mt-1 text-sm text-warning-700 dark:text-warning-300">
                    You've reached your plan limit of {{ number_format($limit) }} {{ strtolower($featureName) }}. 
                    Upgrade your plan to add more.
                </p>
                <div class="mt-3">
                    <a href="{{ route('filament.tenant.pages.billing') }}" 
                       class="inline-flex items-center px-4 py-2 bg-warning-600 hover:bg-warning-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        Upgrade Now
                    </a>
                </div>
            </div>
        </div>
    </div>
@elseif($limit && $limit !== -1)
    <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-info-800 dark:text-info-200">
                {{ $featureName }} Usage
            </span>
            <span class="text-sm text-info-700 dark:text-info-300">
                {{ number_format($currentCount) }} / {{ number_format($limit) }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
            <div class="h-2 rounded-full transition-all duration-300 {{ $percentage() >= 90 ? 'bg-danger-600' : ($percentage() >= 70 ? 'bg-warning-600' : 'bg-success-600') }}" 
                 style="width: {{ $percentage() }}%">
            </div>
        </div>
        @if($remaining() > 0 && $remaining() <= 10)
            <p class="mt-2 text-xs text-warning-600 dark:text-warning-400">
                Only {{ $remaining() }} {{ strtolower($featureName) }} remaining
            </p>
        @endif
    </div>
@endif
