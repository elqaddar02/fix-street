@php($title = 'Dashboard - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-b from-red-50 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
                <p class="mb-4 text-sm font-medium text-red-700 uppercase tracking-wide">Madinova account</p>
                <p class="mb-2 text-lg font-semibold text-gray-900">
                    {{ __('Welcome back,') }} {{ Auth::user()->name }}.
                </p>
                <p class="text-sm text-gray-600">
                    From here you can submit new street reports or track issues already sent to the city maintenance team.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('reports.create') }}"
                   class="rounded-xl border border-red-200 bg-white p-5 hover:border-red-400 hover:bg-red-50 transition">
                    <h3 class="text-lg font-semibold text-gray-900">Create Report</h3>
                    <p class="mt-2 text-sm text-gray-700">
                        Submit a new issue with location, category, and optional photo so teams can act quickly.
                    </p>
                </a>

                <a href="{{ route('reports.index') }}"
                   class="rounded-xl border border-red-200 bg-white p-5 hover:border-red-400 hover:bg-red-50 transition">
                    <h3 class="text-lg font-semibold text-gray-900">View Reports</h3>
                    <p class="mt-2 text-sm text-gray-700">
                        Review the latest reports and check the status of existing issues in your city.
                    </p>
                </a>

                @if (Auth::user()->is_admin)
                    <a href="{{ route('admin.reports.index') }}"
                       class="rounded-xl border border-red-200 bg-white p-5 hover:border-red-400 hover:bg-red-50 transition">
                        <h3 class="text-lg font-semibold text-gray-900">Admin Moderation</h3>
                        <p class="mt-2 text-sm text-gray-700">
                            Access the administrative panel to review, update, and moderate incoming reports.
                        </p>
                    </a>
                @endif
            </div>

            <div class="my-6">
                <x-ad-banner type="horizontal" />
            </div>
        </div>
    </div>
</x-app-layout>
