@extends('admin.layouts.app')

@section('title', 'Tableau de Bord')

@section('content')

<div class="min-h-screen pb-12 space-y-8 font-sans antialiased text-slate-900">

    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-500/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">Dashboard</h1>
            <p class="text-slate-500 font-medium">Vue d'ensemble et monitoring de l'activité.</p>
        </div>

        <!-- <div class="flex items-center gap-3">
            <div class="relative group hidden sm:block">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Recherche rapide..." class="pl-10 pr-4 py-2.5 bg-white/50 backdrop-blur-md border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all outline-none w-64 shadow-sm">
            </div>

            <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white rounded-2xl text-sm font-semibold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exporter
            </button>
        </div> -->
    </div>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $cards = [
                [
                    'title' => 'Utilisateurs', 
                    'value' => $totalUsers, 
                    'trend' => '+12%', 
                    'trend_up' => true,
                    'subtext' => 'vs mois dernier',
                    'color' => 'blue', 
                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
                ],
                [
                    'title' => 'Signalements', 
                    'value' => $totalReports, 
                    'trend' => '+5.2%', 
                    'trend_up' => true,
                    'subtext' => 'Nouveaux ce jour',
                    'color' => 'amber', 
                    'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
                ],
                [
                    'title' => 'Commentaires', 
                    'value' => $totalComments, 
                    'trend' => '-2%', 
                    'trend_up' => false,
                    'subtext' => 'Modération active',
                    'color' => 'emerald', 
                    'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'
                ],
                [
                    'title' => 'Catégories', 
                    'value' => $totalCategories, 
                    'trend' => 'Stable', 
                    'trend_up' => true,
                    'subtext' => 'Secteurs actifs',
                    'color' => 'indigo', 
                    'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'
                ],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="group relative bg-white border border-slate-100 rounded-[2rem] p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-16 h-16 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $card['icon'] }}"></path></svg>
            </div>

            <div class="relative space-y-4">
                <div class="flex items-center justify-between">
                    <div class="p-3 rounded-2xl bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"></path></svg>
                    </div>
                    
                    <span class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $card['trend_up'] ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        @if($card['trend_up'])
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                        @endif
                        {{ $card['trend'] }}
                    </span>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $card['title'] }}</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-4xl font-black text-slate-900 tracking-tight counter" data-target="{{ $card['value'] }}">0</span>
                        <span class="text-xl font-bold text-{{ $card['color'] }}-500">+</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                    <p class="text-xs font-medium text-slate-500">{{ $card['subtext'] }}</p>
                    <div class="flex -space-x-2">
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-200"></div>
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-300"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        
        <div class="lg:col-span-1 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-slate-800">Distribution</h2>
                <p class="text-sm text-slate-500">Répartition par statut</p>
            </div>

            <div class="relative h-64 mb-8">
                <canvas id="statusChart"></canvas>
            </div>

            <div class="space-y-3">
                @php
                    $statusStyles = [
                        'OPEN' => ['label' => 'Ouverts', 'color' => 'bg-amber-500'],
                        'IN_PROGRESS' => ['label' => 'En cours', 'color' => 'bg-blue-500'],
                        'RESOLVED' => ['label' => 'Résolus', 'color' => 'bg-emerald-500'],
                        'REJECTED' => ['label' => 'Rejetés', 'color' => 'bg-rose-500'],
                    ];
                @endphp
                @foreach($statusStyles as $key => $style)
                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full {{ $style['color'] }}"></span>
                        <span class="text-sm font-semibold text-slate-600">{{ $style['label'] }}</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800">{{ $statusCounts[$key] ?? 0 }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Top Catégories</h2>
                    <p class="text-sm text-slate-500">Volume de signalements</p>
                </div>
            </div>
            <div class="h-full max-h-[400px]">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm overflow-hidden">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Activités Récentes</h2>
            <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-slate-100">
                    @foreach($latestReports as $report)
                    <li class="py-5 hover:bg-slate-50/50 px-2 rounded-2xl transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $report->title }}</p>
                                <p class="text-xs text-slate-500">{{ $report->city->name ?? 'Ville inconnue' }}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                    @if($report->status === 'OPEN') bg-amber-100 text-amber-700
                                    @elseif($report->status === 'IN_PROGRESS') bg-blue-100 text-blue-700
                                    @elseif($report->status === 'RESOLVED') bg-emerald-100 text-emerald-700
                                    @else bg-rose-100 text-rose-700 @endif">
                                    {{ $report->status }}
                                </span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-6">Nouveaux Membres</h2>
            <div class="space-y-6">
                @foreach($latestUsers as $user)
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    window.statusCounts = {!! json_encode(array_values($statusCounts)) !!};
    window.categoryLabels = {!! json_encode(array_keys($reportsByCategory)) !!};
    window.categoryData = {!! json_encode(array_values($reportsByCategory)) !!};
</script>
@vite('resources/js/dashboard.js')
@endsection