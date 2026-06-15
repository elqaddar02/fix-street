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
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all mb-6">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        {{ __('Back to Reports') }}
                    </a>
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

                            <!-- Like Section -->
                            @auth
                                @php
                                    $userHasLiked = auth()->user()->likedReports()->where('report_id', $report->id)->exists();
                                @endphp
                                <div x-data="reportLike({{ $report->id }}, {{ $userHasLiked ? 'true' : 'false' }}, {{ $report->likes_count ?? 0 }})" class="mb-6 flex items-center gap-3 pb-4 border-b border-gray-200">
                                    <button type="button" @click="toggle()" :class="liked ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all">
                                        <svg class="w-5 h-5" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="font-semibold" x-text="likes + ' ' + (likes === 1 ? 'Like' : 'Likes')"></span>
                                    </button>
                                </div>
                            @else
                                <div class="mb-6 flex items-center gap-3 pb-4 border-b border-gray-200">
                                    <form method="POST" class="flex items-center gap-2" id="like-form-guest">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-lg transition-all bg-gray-100 text-gray-600 hover:bg-gray-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            <span class="font-semibold">{{ $report->likes_count ?? 0 }} {{ $report->likes_count === 1 ? 'Like' : 'Likes' }}</span>
                                        </button>
                                    </form>
                                </div>
                            @endauth

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

                                    <div id="comments-list" class="space-y-3 mb-4">
                                        @forelse($report->comments as $comment)
                                            <div class="border border-red-100 p-3 rounded">
                                                <p class="text-sm font-semibold text-gray-800">{{ $comment->user->name ?? 'Guest' }} <span class="text-gray-500">({{ $comment->created_at->diffForHumans() }})</span></p>
                                                <p class="text-gray-700">{{ $comment->comment }}</p>
                                            </div>
                                        @empty
                                            <p id="no-comments" class="text-gray-500">No comments yet.</p>
                                        @endforelse
                                    </div>

                                    <form id="comment-form" class="space-y-3">
                                        @csrf
                                        <textarea name="comment" rows="3" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Write your comment..." required></textarea>
                                        <div class="flex gap-2 items-center">
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">Post Comment</button>
                                            <span id="comment-loading" class="hidden flex items-center gap-2 text-gray-600">
                                                <svg class="animate-spin h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3 2.353z"></path></svg>
                                                <span>Posting...</span>
                                            </span>
                                        </div>
                                    </form>
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

    <script>
        function reportLike(reportId, initialLiked, initialLikes) {
            return {
                reportId,
                liked: initialLiked,
                likes: initialLikes,
                loading: false,
                async toggle() {
                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const response = await fetch(`/reports/${this.reportId}/likes/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({}),
                        });

                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }

                        const data = await response.json();
                        this.liked = data.liked;
                        this.likes = data.likes_count;
                    } catch (error) {
                        console.error('Like toggle failed:', error);
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }

        // Handle like button redirect for unauthenticated users
        const guestLikeForm = document.getElementById('like-form-guest');
        if (guestLikeForm) {
            guestLikeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const currentUrl = window.location.href;
                window.location.href = '{{ route("login.redirect") }}?intended=' + encodeURIComponent(currentUrl);
            });
        }

        document.getElementById('comment-form').addEventListener('submit', function(e) {
            @if(!auth()->check())
                e.preventDefault();
                const currentUrl = window.location.href;
                window.location.href = '{{ route("login.redirect") }}?intended=' + encodeURIComponent(currentUrl);
            @else
                e.preventDefault();
                const form = this;
                const textarea = form.querySelector('textarea[name="comment"]');
                const submitBtn = form.querySelector('button[type="submit"]');
                const loadingSpan = document.getElementById('comment-loading');
                const reportId = {{ $report->id }};

                if (!textarea.value.trim()) return;

                submitBtn.disabled = true;
                loadingSpan.classList.remove('hidden');

                fetch(`/reports/${reportId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        comment: textarea.value,
                    }),
                })
                .then(response => {
                    if (!response.ok) throw new Error('Failed to post comment');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remove "No comments" message if it exists
                        const noComments = document.getElementById('no-comments');
                        if (noComments) {
                            noComments.remove();
                        }

                        // Create new comment element
                        const commentDiv = document.createElement('div');
                        commentDiv.className = 'border border-red-100 p-3 rounded';
                        commentDiv.innerHTML = `
                            <p class="text-sm font-semibold text-gray-800">${data.comment.user} <span class="text-gray-500">(${data.comment.created_at})</span></p>
                            <p class="text-gray-700">${data.comment.text}</p>
                        `;

                        // Add to comments list
                        document.getElementById('comments-list').appendChild(commentDiv);

                        // Clear textarea
                        textarea.value = '';
                    }
                })
                .catch(error => {
                    console.error('Error posting comment:', error);
                    alert('Failed to post comment. Please try again.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    loadingSpan.classList.add('hidden');
                });
            @endif
        });
    </script>
</x-app-layout>