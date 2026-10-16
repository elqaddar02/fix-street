@php
    $title = $report->title . ' - Madinup';

    $hasImage = $report->image && file_exists(storage_path('app/public/' . $report->image));
    $isOwner = auth()->id() === $report->user_id;
    $districtName = $report->district ? (app()->getLocale() === 'ar' ? $report->district->name_ar : $report->district->name_fr) : null;
    $quartierName = $report->quartier ? (app()->getLocale() === 'ar' ? $report->quartier->name_ar : $report->quartier->name_fr) : null;
    $reportedOn = $report->created_at->copy()->locale(app()->getLocale());

    $card = 'rounded-xl border border-gray-200 bg-white shadow-[0_3px_12px_rgba(17,24,39,0.04)]';
    $cardTitle = 'text-base font-semibold text-gray-900';
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-6">
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900">
                <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                {{ __('nav.all_reports') }}
            </a>

            <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ $report->title }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-gray-500">
                        <x-status-badge :status="$report->status" />
                        <span>{{ $report->category->display_name ?? __('dashboard.labels.unknown') }}</span>
                        <span class="text-gray-300" aria-hidden="true">·</span>
                        <span>{{ $report->city->display_name ?? __('dashboard.labels.unknown') }}</span>
                        <span class="text-gray-300" aria-hidden="true">·</span>
                        <time datetime="{{ $report->created_at->toIso8601String() }}">{{ $reportedOn->translatedFormat('j M Y') }}</time>
                    </div>
                </div>

                @if($isOwner)
                    <a href="{{ route('reports.edit', $report) }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                        {{ __('dashboard.actions.edit') }}
                    </a>
                @endif
            </div>
        </header>


        @php
            $userHasLiked = auth()->check() && auth()->user()->likedReports()->where('report_id', $report->id)->exists();
            $guestConfirmUrl = route('login.redirect', ['intended' => url()->current()]);
            $checkIcon = 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z';
        @endphp

        {{--
            One set of sections. On phones the two column wrappers dissolve
            (display: contents) so the sections can be ordered photo → details →
            description → map; on desktop they are the main column and sidebar.
        --}}
        <div class="flex flex-col gap-4 lg:grid lg:grid-cols-3 lg:gap-6">
            {{-- MAIN COLUMN --}}
            <div class="contents lg:block lg:col-span-2 lg:space-y-6">
                <figure class="{{ $card }} order-1 overflow-hidden">
                    @if($hasImage)
                        <img src="{{ asset('storage/' . $report->image) }}" alt="{{ $report->title }}" class="aspect-[4/3] w-full bg-gray-100 object-cover sm:aspect-[16/10]">
                    @else
                        <div class="flex aspect-[16/9] w-full items-center justify-center bg-gray-50 text-gray-300">
                            <svg class="h-14 w-14" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                        </div>
                    @endif
                </figure>

                {{-- Identical to the title for reports made with the current
                     form, so only worth a section of its own when it differs. --}}
                @if(filled($report->description) && $report->description !== $report->title)
                    <section class="{{ $card }} order-3 p-5 sm:p-6">
                        <h2 class="{{ $cardTitle }}">{{ __('report.description') }}</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $report->description }}</p>
                    </section>
                @endif

                @if(config('features.comments'))
                    <section class="{{ $card }} order-5 p-5 sm:p-6">
                        <h2 class="{{ $cardTitle }}">{{ __('report.comments') }}</h2>

                        <div id="comments-list" class="mt-4 space-y-3">
                            @forelse($report->visibleComments as $comment)
                                <div class="rounded-lg border border-gray-200 px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ $comment->user->name ?? 'Guest' }} <span class="font-normal text-gray-500">({{ $comment->created_at->diffForHumans() }})</span></p>
                                    <p class="mt-1 text-sm text-gray-700">{{ $comment->comment }}</p>
                                </div>
                            @empty
                                <p id="no-comments" class="text-sm text-gray-500">No comments yet.</p>
                            @endforelse
                        </div>

                        <form id="comment-form" class="mt-4 space-y-3">
                            @csrf
                            <textarea name="comment" rows="3" class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Write your comment..." required></textarea>
                            <div class="flex items-center gap-3">
                                <x-primary-button>Post Comment</x-primary-button>
                                <span id="comment-loading" class="hidden items-center gap-2 text-sm text-gray-500">
                                    <svg class="h-4 w-4 animate-spin text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3 2.353z"></path></svg>
                                    <span>Posting...</span>
                                </span>
                            </div>
                        </form>
                    </section>
                @endif
            </div>

            {{-- SIDEBAR --}}
            <aside class="contents lg:block lg:space-y-6">
                {{--
                    Confirmations, not "likes": the count means "this many
                    neighbours confirm the problem is real and still there",
                    which is the part a municipality can act on.
                    Desktop only; phones get the sticky bar below.
                --}}
                <section class="{{ $card }} hidden p-5 sm:p-6 lg:block">
                    <p class="text-sm text-gray-500">{{ __('report.confirm_hint') }}</p>
                    @auth
                        <div x-data="reportLike({{ $report->id }}, @js($userHasLiked), {{ $report->likes_count ?? 0 }})" class="mt-4">
                            <button type="button" @click="toggle()" :disabled="loading"
                                    :class="liked ? 'border-red-200 bg-red-50 text-red-700 hover:bg-red-100' : 'border-red-600 bg-red-600 text-white hover:bg-red-700'"
                                    :aria-pressed="liked ? 'true' : 'false'"
                                    class="flex w-full items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors disabled:opacity-60">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkIcon }}"/></svg>
                                <span x-text="liked ? @js(__('report.confirmed')) : @js(__('report.confirm'))"></span>
                            </button>
                            <p class="mt-2 text-center text-sm font-medium text-gray-700" x-text="countLabel()"></p>
                            <p x-show="failed" x-cloak class="mt-2 text-center text-sm text-rose-600" role="alert">{{ __('report.confirm_failed') }}</p>
                        </div>
                    @else
                        <a href="{{ $guestConfirmUrl }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkIcon }}"/></svg>
                            {{ __('report.confirm') }}
                        </a>
                        <p class="mt-2 text-center text-sm font-medium text-gray-700">
                            {{ trans_choice('report.confirmations_count', $report->likes_count ?? 0, ['count' => $report->likes_count ?? 0]) }}
                        </p>
                    @endauth
                </section>

                <section class="{{ $card }} order-2 p-5 sm:p-6">
                    <h2 class="{{ $cardTitle }}">{{ __('report.details') }}</h2>
                    <dl class="mt-4 divide-y divide-gray-100 text-sm">
                        @foreach(array_filter([
                            __('dashboard.table.headers.status') => 'status',
                            __('Category') => $report->category->display_name ?? null,
                            __('City') => $report->city->display_name ?? null,
                            __('District / Arrondissement') => $districtName,
                            __('Quartier') => $quartierName,
                            __('report.reported_by') => $report->user->name ?? null,
                            __('report.reported_on') => $reportedOn->translatedFormat('j F Y, H:i'),
                        ]) as $label => $value)
                            <div class="flex items-center justify-between gap-4 py-2.5 first:pt-0 last:pb-0">
                                <dt class="text-gray-500">{{ $label }}</dt>
                                <dd class="min-w-0 truncate text-end font-medium text-gray-900">
                                    @if($value === 'status')
                                        <x-status-badge :status="$report->status" />
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                @if($report->latitude && $report->longitude)
                    <section class="{{ $card }} order-4 overflow-hidden">
                        <div class="flex items-baseline justify-between gap-3 px-5 pt-5 sm:px-6">
                            <h2 class="{{ $cardTitle }}">{{ __('Location') }}</h2>
                            <span class="text-xs tabular-nums text-gray-500" dir="ltr">{{ number_format($report->latitude, 4) }}, {{ number_format($report->longitude, 4) }}</span>
                        </div>
                        <div id="report-map" class="mt-4 h-56 w-full border-t border-gray-200" data-lat="{{ $report->latitude }}" data-lng="{{ $report->longitude }}"></div>
                    </section>
                @endif
            </aside>
        </div>
    </div>

    {{-- Phones: the main action stays in reach (the layout pins this above the tab bar). --}}
    <x-slot name="stickyBar">
        @auth
            <div x-data="reportLike({{ $report->id }}, @js($userHasLiked), {{ $report->likes_count ?? 0 }})" class="flex items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900" x-text="countLabel()"></p>
                    <p x-show="!failed" class="truncate text-xs text-gray-500">{{ __('report.confirm_hint') }}</p>
                    <p x-show="failed" x-cloak class="truncate text-xs text-rose-600" role="alert">{{ __('report.confirm_failed') }}</p>
                </div>
                <button type="button" @click="toggle()" :disabled="loading"
                        :class="liked ? 'border-red-200 bg-red-50 text-red-700' : 'border-red-600 bg-red-600 text-white'"
                        :aria-pressed="liked ? 'true' : 'false'"
                        class="inline-flex shrink-0 items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-semibold shadow-sm transition-colors disabled:opacity-60">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkIcon }}"/></svg>
                    <span x-text="liked ? @js(__('report.confirmed')) : @js(__('report.confirm'))"></span>
                </button>
            </div>
        @else
            <div class="flex items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900">{{ trans_choice('report.confirmations_count', $report->likes_count ?? 0, ['count' => $report->likes_count ?? 0]) }}</p>
                    <p class="truncate text-xs text-gray-500">{{ __('report.confirm_hint') }}</p>
                </div>
                <a href="{{ $guestConfirmUrl }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $checkIcon }}"/></svg>
                    {{ __('report.confirm') }}
                </a>
            </div>
        @endauth
    </x-slot>

    <script>
        // Read-only map of where the report was filed. Leaflet is loaded by the app layout.
        (function () {
            const el = document.getElementById('report-map');
            if (!el || typeof L === 'undefined') return;
            const point = [parseFloat(el.dataset.lat), parseFloat(el.dataset.lng)];
            const map = L.map(el, { scrollWheelZoom: false }).setView(point, 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            L.marker(point).addTo(map);
        })();

        // Server-rendered wording for each count, so the label stays translated
        // (and correctly pluralised) after the count changes client-side.
        const CONFIRMATION_LABELS = {
            zero: @js(trans_choice('report.confirmations_count', 0, ['count' => 0])),
            one: @js(trans_choice('report.confirmations_count', 1, ['count' => 1])),
            many: @js(trans_choice('report.confirmations_count', 2, ['count' => ':count'])),
        };

        function reportLike(reportId, initialLiked, initialLikes) {
            return {
                reportId,
                liked: initialLiked,
                likes: initialLikes,
                loading: false,
                failed: false,
                countLabel() {
                    if (this.likes === 0) return CONFIRMATION_LABELS.zero;
                    if (this.likes === 1) return CONFIRMATION_LABELS.one;
                    return CONFIRMATION_LABELS.many.replace(':count', this.likes);
                },
                async toggle() {
                    if (this.loading) return;
                    this.loading = true;
                    this.failed = false;

                    try {
                        const response = await fetch(@js(route('reports.likes.toggle', ['report' => '__ID__'])).replace('__ID__', this.reportId), {
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
                        this.failed = true;
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }


        // Absent while the comments feature is switched off.
        document.getElementById('comment-form')?.addEventListener('submit', function(e) {
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
                loadingSpan.classList.replace('hidden', 'flex');

                fetch(@js(route('reports.comments.store', ['report' => '__ID__'])).replace('__ID__', reportId), {
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
                        commentDiv.className = 'rounded-lg border border-gray-200 px-4 py-3';

                        // Build with textContent so user-written text is never interpreted as HTML.
                        const meta = document.createElement('p');
                        meta.className = 'text-sm font-semibold text-gray-900';
                        meta.textContent = data.comment.user + ' ';
                        const time = document.createElement('span');
                        time.className = 'font-normal text-gray-500';
                        time.textContent = `(${data.comment.created_at})`;
                        meta.appendChild(time);

                        const text = document.createElement('p');
                        text.className = 'mt-1 text-sm text-gray-700';
                        text.textContent = data.comment.text;

                        commentDiv.append(meta, text);

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
                    loadingSpan.classList.replace('flex', 'hidden');
                });
            @endif
        });
    </script>
</x-app-layout>