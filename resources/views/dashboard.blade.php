<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-6">{{ __("You're logged in!") }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('reports.create') }}"
                           class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50 transition">
                            <h3 class="text-lg font-semibold text-gray-900">Create Report</h3>
                            <p class="mt-1 text-sm text-gray-600">Submit a new issue report with category, city, and optional location details.</p>
                        </a>

                        @if (Auth::user()->is_admin)
                            <a href="{{ route('admin.reports.index') }}"
                               class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                <h3 class="text-lg font-semibold text-gray-900">Manage Reports</h3>
                                <p class="mt-1 text-sm text-gray-600">Review incoming reports and update their status.</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
