<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('All Reports') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-red-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-6">
                <aside class="lg:col-span-1 bg-white border border-red-200 rounded-xl p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-red-800 mb-4">{{ __('Filter Reports') }}</h3>
                    <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }}</label>
                            <select name="category" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:ring-2 focus:ring-red-200">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date') }}</label>
                            <select name="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:ring-2 focus:ring-red-200">
                                <option value="">{{ __('All Dates') }}</option>
                                <option value="today" @selected($selectedDate === 'today')>{{ __('Today') }}</option>
                                <option value="week" @selected($selectedDate === 'week')>{{ __('This Week') }}</option>
                                <option value="month" @selected($selectedDate === 'month')>{{ __('This Month') }}</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">{{ __('Filter') }}</button>
                            <a href="{{ route('reports.index') }}" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-yellow-300">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </aside>

                <section class="lg:col-span-3 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                        <div>
                            <h3 class="text-xl font-bold text-red-800">{{ $reports->total() }} {{ __('Reports found') }}</h3>
                            <p class="text-sm text-slate-500">{{ __('Explore reported street issues, sorted by neighborhood and status.') }}</p>
                        </div>
                        <a href="{{ route('reports.create') }}" class="rounded-lg bg-yellow-400 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-yellow-300">{{ __('Create Report') }}</a>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-5">
                        @forelse ($reports as $report)
                            <article class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm hover:shadow-md transition">
                                @if($report->image)
                                    <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="w-full h-32 object-cover rounded-lg mb-3">
                                @endif
                                <div class="flex justify-between items-start gap-2 mb-3">
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-red-700">{{ $report->status }}</span>
                                    <span class="text-xs text-gray-500">{{ $report->created_at->format('M d, Y') }}</span>
                                </div>
                                <h4 class="text-lg font-bold text-red-800 mb-2">{{ $report->title }}</h4>
                                <p class="text-sm text-slate-700 mb-3">{{ \Illuminate\Support\Str::limit($report->description, 110) }}</p>
                                <div class="flex flex-wrap gap-2 text-xs font-medium">
                                    <span class="rounded-full bg-yellow-100 px-2 py-1 text-yellow-800">{{ $report->category->name }}</span>
                                    <span class="rounded-full bg-yellow-100 px-2 py-1 text-yellow-800">{{ $report->city->name }}</span>
                                </div>
                                <div class="mt-4 text-xs text-slate-500">{{ __('Reported by') }} {{ $report->user->name }}</div>
                                <a href="{{ route('reports.show', $report) }}" class="mt-3 inline-block text-sm font-semibold text-red-600 hover:text-red-800">{{ __('View details') }} →</a>
                            </article>
                        @empty
                            <div class="col-span-full rounded-xl border border-red-200 bg-white p-6 text-center text-slate-600">
                                {{ __('No reports yet.') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="pt-4">
                        {{ $reports->links() }}
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
