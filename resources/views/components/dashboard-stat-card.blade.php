@props([
    'icon' => 'fas fa-chart-line',
    'title' => 'Métrica',
    'value' => '0',
    'trend' => null,
    'trendValue' => null,
    'color' => 'blue',
    'actionUrl' => null,
    'actionLabel' => 'Ver más'
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-50 text-blue-700 border-blue-200',
        'green' => 'bg-green-50 text-green-700 border-green-200',
        'red' => 'bg-red-50 text-red-700 border-red-200',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
        'amber' => 'bg-amber-50 text-amber-700 border-amber-200',
        'cyan' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
        'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    ];

    $iconBgClasses = [
        'blue' => 'bg-blue-100 text-blue-600',
        'green' => 'bg-green-100 text-green-600',
        'red' => 'bg-red-100 text-red-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'cyan' => 'bg-cyan-100 text-cyan-600',
        'indigo' => 'bg-indigo-100 text-indigo-600',
    ];

    $colorClass = $colorClasses[$color] ?? $colorClasses['blue'];
    $iconBgClass = $iconBgClasses[$color] ?? $iconBgClasses['blue'];
@endphp

<div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
    <!-- Card Content -->
    <div class="p-6">
        <div class="flex items-start justify-between">
            <!-- Left: Icon & Info -->
            <div class="flex items-start gap-4 flex-1">
                <!-- Icon -->
                <div class="{{ $iconBgClass }} w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="{{ $icon }} text-lg"></i>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">{{ $title }}</p>

                    <div class="flex items-baseline gap-2 mt-2">
                        <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>

                        @if($trend && $trendValue)
                        <span @class([
                            'inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full',
                            'text-green-700 bg-green-50' => $trend === 'up',
                            'text-red-700 bg-red-50' => $trend === 'down',
                        ])>
                            @if($trend === 'up')
                            <i class="fas fa-arrow-trend-up"></i>
                            @elseif($trend === 'down')
                            <i class="fas fa-arrow-trend-down"></i>
                            @endif
                            {{ $trendValue }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Action -->
    @if($actionUrl)
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
        <a
            href="{{ $actionUrl }}"
            class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
            {{ $actionLabel }}
        </a>
        <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
    </div>
    @endif
</div>
