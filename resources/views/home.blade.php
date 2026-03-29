@php($title = 'Madinova - Official City Street Maintenance Portal')

<x-app-layout>
    <style>
        .gov-header { background-color: #7f1d1d; }
        .gov-primary { background-color: #b91c1c; color: white; }
        .gov-secondary { background-color: #dc2626; color: white; }
        .gov-accent { background-color: #dc2626; color: white; }
        .gov-light-bg { background-color: #fef2f2; }
        .gov-border { border-color: #e2e8f0; }
        .gov-text-primary { color: #374151; }
        .gov-text-secondary { color: #64748b; }
        .gov-button { border: 2px solid; transition: all 0.2s ease; }
        .gov-button:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>

    <section class="bg-gradient-to-r from-red-50 to-red-100 py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="mb-6">
                        <span class="inline-block bg-red-600 text-white px-3 py-1 text-sm font-medium uppercase tracking-wide">Official City Service</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">Report Street Maintenance Issues</h1>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        Submit official reports for damaged roads, broken streetlights, potholes, and other public infrastructure issues.
                        Your reports help maintain safe streets for all citizens.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="gov-button gov-primary px-8 py-4 text-center font-semibold hover:bg-red-800">Report a Problem</a>
                        <a href="#reports" class="gov-button border-gray-300 px-8 py-4 text-center hover:bg-gray-50">View Recent Reports</a>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset('assets/hero-street.svg') }}" alt="City street maintenance and repair" class="w-full h-80 object-cover shadow-lg" />
                    <div class="absolute inset-0 bg-red-600 opacity-10"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6">
        <div class="my-6">
            <x-ad-banner type="horizontal" />
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Report Categories</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Select the type of street maintenance issue you wish to report</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Road Damage</h3>
                    <p class="text-gray-600">Report potholes, cracks, and other road surface issues</p>
                </div>

                <div class="border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Street Lighting</h3>
                    <p class="text-gray-600">Report broken or malfunctioning street lights</p>
                </div>

                <div class="border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                    <div class="w-12 h-12 bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Infrastructure</h3>
                    <p class="text-gray-600">Report damaged signs, barriers, and other street infrastructure</p>
                </div>
            </div>
        </div>
    </section>

    <section id="reports" class="py-16 gov-light-bg">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How to File a Street Report</h2>
                <p class="text-gray-600">Follow these simple steps to help city maintenance teams act quickly.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Report Submission Process</h3>
                    <ol class="list-decimal pl-5 space-y-4 text-gray-700">
                        <li><strong>Choose report type</strong> (Road Damage, Street Lighting, Infrastructure).</li>
                        <li><strong>Describe the issue</strong> clearly with location, severity, and details.</li>
                        <li><strong>Upload evidence</strong> (photo optional but very helpful for inspection).</li>
                        <li><strong>Submit</strong> and track response status from the portal.</li>
                    </ol>
                    <div class="mt-6">
                        <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="gov-button gov-primary px-6 py-2 inline-block font-semibold hover:bg-red-800">Start a New Report</a>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse ($latestReports as $report)
                        <article class="bg-white border border-gray-200 p-4 flex items-center h-36">
                            <img src="{{ asset('assets/report-placeholder.svg') }}" alt="{{ $report->title }}" class="w-28 h-full object-cover border border-red-200" />
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate">{{ $report->title }}</h3>
                                <p class="text-sm text-gray-600 truncate">{{ Str::limit($report->description, 80) }}</p>
                                <div class="text-xs text-red-700 mt-2">{{ $report->category->name }} � {{ $report->city->name }} � {{ $report->created_at->format('M j, Y') }}</div>
                            </div>
                            <button type="button" class="gov-button gov-secondary px-4 py-2 text-sm font-medium hover:bg-red-700 report-open-modal" data-report-id="{{ $report->id }}">View</button>
                        </article>
                    @empty
                        <div class="bg-white border border-gray-200 p-6 text-center"><p class="text-gray-600">No reports currently. Your contribution can help.</p></div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- <section class="py-16 bg-slate-900 text-white">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold mb-4">Contribute to a Safer City</h2>
            <p class="text-slate-300 text-lg mb-8 leading-relaxed">Your reports help city officials prioritize and address street maintenance issues efficiently. Together, we can maintain safe and well-maintained public infrastructure.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ auth()->check() ? route('reports.create') : route('login') }}" class="gov-button bg-white text-slate-900 px-8 py-4 font-semibold hover:bg-gray-100">Report an Issue</a>
                <a href="{{ route('reports.index') }}" class="gov-button border-white text-white px-8 py-4 hover:bg-white hover:text-slate-900">View All Reports</a>
            </div>
        </div>
    </section> -->

    <div id="home-server-data" data-latest-reports='@json($latestReports ?? [])' data-authenticated='{{ auth()->check() ? 'true' : 'false' }}' style="display:none;"></div>

    <div id="report-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-3xl shadow-2xl border border-red-200 p-6 relative">
            <button id="report-modal-close" class="absolute top-4 right-4 text-red-700 font-bold">X</button>
            <h3 id="modal-title" class="text-2xl font-bold text-gray-900 mb-2">Report title</h3>
            <p id="modal-status" class="text-sm text-red-700 font-semibold mb-2">Status</p>
            <p id="modal-category" class="text-sm text-gray-600 mb-1">Category</p>
            <p id="modal-city" class="text-sm text-gray-600 mb-1">City</p>
            <p id="modal-description" class="text-gray-700 mb-4">Description goes here</p>
            <p id="modal-user" class="text-sm text-gray-500 mb-4">Reported by</p>

            <div class="mb-4">
                <h4 class="font-semibold text-gray-800">Comments</h4>
                <div id="modal-comments" class="mt-3 space-y-2 max-h-60 overflow-y-auto"></div>
            </div>

            <form id="modal-comment-form" method="POST" action="">
                @csrf
                <div class="mb-3">
                    <textarea id="modal-comment-input" name="comment" rows="3" class="w-full border border-gray-300 p-2" placeholder="Add your comment"></textarea>
                </div>
                <button id="modal-comment-submit" type="submit" class="gov-button gov-secondary px-4 py-2 font-medium">Send comment</button>
            </form>
        </div>
    </div>

    <script>
        const serverData = document.getElementById('home-server-data');
        const latestReports = serverData ? JSON.parse(serverData.dataset.latestReports || '[]') : [];
        const isAuthenticated = serverData ? serverData.dataset.authenticated === 'true' : false;

        const reportModal = document.getElementById('report-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalStatus = document.getElementById('modal-status');
        const modalCategory = document.getElementById('modal-category');
        const modalCity = document.getElementById('modal-city');
        const modalDescription = document.getElementById('modal-description');
        const modalUser = document.getElementById('modal-user');
        const modalComments = document.getElementById('modal-comments');
        const modalCommentForm = document.getElementById('modal-comment-form');
        const modalCommentInput = document.getElementById('modal-comment-input');

        function closeReportModal() {
            reportModal.classList.add('hidden');
            reportModal.classList.remove('flex');
        }

        document.querySelectorAll('.report-open-modal').forEach((button) => {
            button.addEventListener('click', function () {
                const reportId = Number(this.dataset.reportId);
                const report = latestReports.find(r => r.id === reportId);
                if (!report) return;

                modalTitle.textContent = report.title;
                modalStatus.textContent = report.status;
                modalCategory.textContent = 'Category: ' + report.category;
                modalCity.textContent = 'City: ' + report.city;
                modalDescription.textContent = report.description;
                modalUser.textContent = 'Reported by: ' + report.user + ' on ' + report.created_at;

                modalComments.innerHTML = report.comments.length
                    ? report.comments.map(c => `<div class="p-2 border border-red-100 rounded"><p class="text-sm"><strong>${c.user}</strong> <span class="text-gray-500">${c.created_at}</span></p><p class="text-gray-700 text-sm">${c.comment}</p></div>`).join('')
                    : '<p class="text-gray-500">No comments yet.</p>';

                modalCommentForm.action = '/reports/' + report.id + '/comments';
                modalCommentInput.value = '';

                reportModal.classList.remove('hidden');
                reportModal.classList.add('flex');
            });
        });

        document.getElementById('report-modal-close').addEventListener('click', closeReportModal);

        modalCommentForm.addEventListener('submit', function (event) {
            if (!isAuthenticated) {
                event.preventDefault();
                alert('Please log in to post a comment.');
                window.location.href = '/login';
            }
        });
    </script>
</x-app-layout>
