<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') - Madinova</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-red-900 text-white sticky top-0 h-screen shadow-lg flex flex-col">
            <div class="px-6 py-5 border-b border-red-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="h-8 w-8 text-white" />
                    <span class="font-bold text-lg">Madinova Admin</span>
                </a>
            </div>

            <nav class="mt-4 px-2 space-y-1 flex-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Users</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Reports</a>
                <a href="{{ route('admin.comments.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.comments.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Comments</a>
                <a href="{{ route('admin.cities.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.cities.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Cities</a>
                <a href="{{ route('admin.quartiers.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.quartiers.*') ? 'bg-red-700 text-white' : 'text-red-100 hover:bg-red-800 hover:text-white' }}">Quartiers</a>
            </nav>

            <!-- Bottom Actions -->
            <div class="mt-auto px-2 py-4 border-t border-red-800">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 rounded-lg bg-red-700 text-white hover:bg-red-600 transition-colors font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 p-6 lg:p-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-700">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-confirm-change]').forEach(function (element) {
                element.addEventListener('change', function () {
                    var message = this.dataset.confirmMessage || 'Confirmer la modification ?';
                    var previous = this.dataset.current;
                    if (confirm(message)) {
                        this.form.submit();
                    } else {
                        this.value = previous;
                    }
                });
            });

            const STATUS_STYLES = {
                OPEN: { label: 'Ouvert', badge: 'bg-amber-100 text-amber-800 border border-amber-300', border: 'border-amber-400 focus:ring-amber-200', bgColor: '#fef3c7', textColor: '#92400e' },
                IN_PROGRESS: { label: 'En cours', badge: 'bg-sky-100 text-sky-800 border border-sky-300', border: 'border-sky-400 focus:ring-sky-200', bgColor: '#e0f2fe', textColor: '#0c4a6e' },
                RESOLVED: { label: 'Résolu', badge: 'bg-emerald-100 text-emerald-800 border border-emerald-300', border: 'border-emerald-400 focus:ring-emerald-200', bgColor: '#dcfce7', textColor: '#14532d' },
                REJECTED: { label: 'Rejeté', badge: 'bg-rose-100 text-rose-800 border border-rose-300', border: 'border-rose-400 focus:ring-rose-200', bgColor: '#ffe4e6', textColor: '#9f1239' },
                ACTIVE: { label: 'Actif', badge: 'bg-emerald-100 text-emerald-800 border border-emerald-300', border: 'border-emerald-400 focus:ring-emerald-200', bgColor: '#dcfce7', textColor: '#14532d' },
                INACTIVE: { label: 'Inactif', badge: 'bg-slate-100 text-slate-700 border border-slate-300', border: 'border-slate-400 focus:ring-slate-200', bgColor: '#f8fafc', textColor: '#334155' },
                DEFAULT: { label: 'Non défini', badge: 'bg-slate-100 text-slate-700 border border-slate-300', border: 'border-slate-400 focus:ring-slate-200', bgColor: '#f8fafc', textColor: '#334155' }
            };

            function getStatusKey(select) {
                if (select.name === 'active') {
                    return select.value === '1' ? 'ACTIVE' : 'INACTIVE';
                }
                return select.value || 'DEFAULT';
            }

            function updateStatusSelect(select) {
                const status = getStatusKey(select);
                const style = STATUS_STYLES[status] || STATUS_STYLES.DEFAULT;
                select.classList.remove('border-amber-400', 'border-sky-400', 'border-emerald-400', 'border-rose-400', 'border-slate-400', 'focus:ring-amber-200', 'focus:ring-sky-200', 'focus:ring-emerald-200', 'focus:ring-rose-200', 'focus:ring-slate-200');
                select.classList.add(style.border);
                select.style.backgroundColor = style.bgColor;
                select.style.color = style.textColor;

                let badge = select.closest('.status-field')?.querySelector('.status-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'status-badge inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold';
                    const wrapper = select.closest('.status-field') || select.parentNode;
                    wrapper.insertBefore(badge, select);
                }
                badge.className = 'status-badge inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold ' + style.badge;
                badge.textContent = style.label;
            }

            document.querySelectorAll('select.status-select').forEach(function (select) {
                updateStatusSelect(select);
                select.addEventListener('change', function () {
                    updateStatusSelect(this);
                });
            });
        });
    </script>
</body>
</html>
