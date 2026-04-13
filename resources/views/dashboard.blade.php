@php
    $title = __('Dashboard') . ' - Madinova';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-red-50/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

            <!-- Welcome Hero Section -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-600 via-red-700 to-red-800 shadow-2xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>

                <div class="relative px-8 py-12 lg:px-12 lg:py-16">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                        <div class="flex-1">
                            <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6">
                                <svg class="w-4 h-4 text-red-200 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-sm font-medium text-white">{{ __('dashboard.welcome.account') }}</span>
                            </div>

                            <h1 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                                {{ __('Welcome back,') }} <span class="text-yellow-300">{{ Auth::user()->name }}</span>
                            </h1>

                            <p class="text-lg text-red-100 leading-relaxed max-w-2xl">
                                {{ __('dashboard.welcome.message') }}
                            </p>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="relative">
                                <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        {{ __('dashboard.quick_actions.title') }}
                    </h3>
                </div>

                <div class="p-8">
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('reports.create') }}"
                           class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:from-red-700 hover:to-red-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-2xl">
                            <div class="absolute inset-0 bg-white/20 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                            <svg class="w-5 h-5 mr-3 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="relative z-10">{{ __('dashboard.quick_actions.add_report') }}</span>

                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Reports -->
                <x-dashboard.stat-card
                    title="{{ __('dashboard.stats.total') }}"
                    :value="$totalReports"
                    bg="bg-gradient-to-br from-blue-500 to-blue-600"
                    color="text-white"
                    icon="total">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </x-dashboard.stat-card>

                <!-- Open Reports -->
                <x-dashboard.stat-card
                    title="{{ __('dashboard.stats.open') }}"
                    :value="$openReports"
                    bg="bg-gradient-to-br from-yellow-500 to-orange-500"
                    color="text-white"
                    icon="open">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-dashboard.stat-card>

                <!-- In Progress Reports -->
                <x-dashboard.stat-card
                    title="{{ __('dashboard.stats.in_progress') }}"
                    :value="$inProgressReports"
                    bg="bg-gradient-to-br from-orange-500 to-red-500"
                    color="text-white"
                    icon="progress">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </x-dashboard.stat-card>

                <!-- Resolved Reports -->
                <x-dashboard.stat-card
                    title="{{ __('dashboard.stats.resolved') }}"
                    :value="$resolvedReports"
                    bg="bg-gradient-to-br from-green-500 to-emerald-600"
                    color="text-white"
                    icon="resolved">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </x-dashboard.stat-card>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Status Chart -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                            {{ __('dashboard.charts.status.title') }}
                        </h3>
                    </div>
                    <div class="p-8">
                        <canvas id="statusChart" width="400" height="300"></canvas>
                    </div>
                </div>

                <!-- Category Chart -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                            {{ __('dashboard.charts.category.title') }}
                        </h3>
                    </div>
                    <div class="p-8">
                        <canvas id="categoryChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ __('dashboard.table.title') }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ __('dashboard.table.subtitle') }}</p>
                        </div>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $reports->count() }} {{ __('dashboard.stats.total') }}</span>
                        </div>
                    </div>
                </div>

                @if($reports->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.title') }}</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.category') }}</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.city') }}</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.status') }}</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.created') }}</th>
                                    <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('dashboard.table.headers.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reports as $report)
                                    <x-dashboard.report-row :report="$report" />
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-8 py-16 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('dashboard.empty.title') }}</h3>
                            <p class="text-gray-600 mb-8">{{ __('dashboard.empty.message') }}</p>
                            <a href="{{ route('reports.create') }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-red-700 hover:to-red-800 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('dashboard.empty.action') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @php
        $chartData = [
            'stats' => [
                'open' => $openReports,
                'inProgress' => $inProgressReports,
                'resolved' => $resolvedReports,
                'closed' => $closedReports,
            ],
            'categories' => [
                'labels' => array_keys($reportsByCategory),
                'values' => array_values($reportsByCategory),
            ]
        ];
    @endphp

    <script>
        // Chart data injected from Laravel
        window.chartData = <?php echo json_encode($chartData); ?>;
        window.dashboardTranslations = <?php echo json_encode([
            'status_open' => __('dashboard.status.open'),
            'status_in_progress' => __('dashboard.status.in_progress'),
            'status_resolved' => __('dashboard.status.resolved'),
            'status_closed' => __('dashboard.status.closed'),
            'category_dataset' => __('dashboard.charts.category.dataset'),
        ]); ?>;
    </script>

    <script type="module">
        // Initialize Status Chart
        initStatusChart();

        // Initialize Category Chart
        initCategoryChart();

        /**
         * Initialize the Status Doughnut Chart
         */
        function initStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: [
                        window.dashboardTranslations.status_open,
                        window.dashboardTranslations.status_in_progress,
                        window.dashboardTranslations.status_resolved,
                        window.dashboardTranslations.status_closed,
                    ],
                    datasets: [{
                        data: [
                            window.chartData.stats.open,
                            window.chartData.stats.inProgress,
                            window.chartData.stats.resolved,
                            window.chartData.stats.closed,
                        ],
                        backgroundColor: [
                            '#fbbf24', // yellow
                            '#fb923c', // orange
                            '#22c55e', // green
                            '#6b7280'  // gray
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }

        /**
         * Initialize the Category Bar Chart
         */
        function initCategoryChart() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: window.chartData.categories.labels,
                    datasets: [{
                        label: window.dashboardTranslations.category_dataset,
                        data: window.chartData.categories.values,
                        backgroundColor: '#dc2626',
                        borderColor: '#b91c1c',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>
