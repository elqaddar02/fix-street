<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('reports.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            Create Report
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Title</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Category</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">City</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">By</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <p class="font-medium">{{ $report->title }}</p>
                                            <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($report->description, 90) }}</p>
                                        </td>
                                        <td class="px-3 py-2">{{ $report->category->name }}</td>
                                        <td class="px-3 py-2">{{ $report->city->name }}</td>
                                        <td class="px-3 py-2">{{ $report->status }}</td>
                                        <td class="px-3 py-2">{{ $report->user->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-600">
                                            No reports yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
