<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Reports') }}
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

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Title</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Reporter</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Category</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">City</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Status</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <p class="font-medium">{{ $report->title }}</p>
                                            <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($report->description, 80) }}</p>
                                        </td>
                                        <td class="px-3 py-2">{{ $report->user->name }}</td>
                                        <td class="px-3 py-2">{{ $report->category->name }}</td>
                                        <td class="px-3 py-2">{{ $report->city->name }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex rounded px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="OPEN" @selected($report->status === 'OPEN')>OPEN</option>
                                                    <option value="IN_PROGRESS" @selected($report->status === 'IN_PROGRESS')>IN_PROGRESS</option>
                                                    <option value="RESOLVED" @selected($report->status === 'RESOLVED')>RESOLVED</option>
                                                </select>
                                                <x-primary-button>Update</x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-600">
                                            No reports found.
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
