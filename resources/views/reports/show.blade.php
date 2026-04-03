<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Report Image -->
                        <div class="lg:w-1/2 mb-6 lg:mb-0 lg:pr-6">
                            <div class="w-full h-64 lg:h-96 bg-gradient-to-br from-red-50 to-orange-50 rounded-lg shadow-md overflow-hidden flex items-center justify-center">
                                @if($report->image && file_exists(storage_path('app/public/' . $report->image)))
                                    <img src="{{ asset('storage/' . $report->image) }}" 
                                         alt="{{ $report->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>

                        <!-- Report Details -->
                        <div class="lg:w-{{ $report->image ? '1/2' : 'full' }}">
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $report->title }}</h1>

                            <div class="space-y-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700">Description</h3>
                                    <p class="text-gray-600 mt-2">{{ $report->description }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">Category</h3>
                                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium mt-1">
                                            {{ $report->category->display_name }}
                                        </span>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">City</h3>
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mt-1">
                                            {{ $report->city->display_name }}
                                        </span>
                                    </div>

                                    @if($report->quartier)
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">Quartier</h3>
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mt-1">
                                            {{ $report->quartier->display_name }}
                                        </span>
                                    </div>
                                    @endif

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">Status</h3>
                                        @php
                                            $statusClass = match ($report->status) {
                                                'OPEN' => 'bg-red-100 text-red-800',
                                                'IN_PROGRESS' => 'bg-orange-100 text-orange-800',
                                                'RESOLVED' => 'bg-green-100 text-green-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="inline-block {{ $statusClass }} px-3 py-1 rounded-full text-sm font-medium mt-1">
                                            {{ $report->status }}
                                        </span>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">Reported By</h3>
                                        <p class="text-gray-600 mt-1">{{ $report->user->name }}</p>
                                    </div>
                                </div>

                                @if($report->latitude && $report->longitude)
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-700">Location</h3>
                                        <p class="text-gray-600 mt-1">
                                            Latitude: {{ $report->latitude }}, Longitude: {{ $report->longitude }}
                                        </p>
                                    </div>
                                @endif

                                <div>
                                    <h3 class="text-lg font-semibold text-gray-700">Reported On</h3>
                                    <p class="text-gray-600 mt-1">{{ $report->created_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>

                                <div class="mt-8">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Comments</h3>

                                    <div class="space-y-3 mb-4">
                                        @forelse($report->comments as $comment)
                                            <div class="border border-red-100 p-3 rounded">
                                                <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name ?? 'Guest' }} <span class="text-gray-500">({{ $comment->created_at->diffForHumans() }})</span></p>
                                                <p class="text-gray-700">{{ $comment->comment }}</p>
                                            </div>
                                        @empty
                                            <p class="text-gray-500">No comments yet.</p>
                                        @endforelse
                                    </div>

                                    @auth
                                        <form method="POST" action="{{ route('reports.comments.store', $report->id) }}" class="space-y-3">
                                            @csrf
                                            <textarea name="comment" rows="3" class="w-full border border-gray-300 p-3" placeholder="Write your comment..."></textarea>
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Post Comment</button>
                                        </form>
                                    @else
                                        <a href="{{ route('login.redirect') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Login to Comment</a>
                                    @endauth
                                </div>
                            </div>

                            <div class="mt-8 flex gap-4">
                                <a href="{{ route('reports.index') }}"
                                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                    Back to Reports
                                </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>