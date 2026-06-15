<!-- Premium Stat Card Component -->
<div class="group relative bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
    <!-- Background gradient overlay -->
    <div class="absolute inset-0 {{ $bg }} opacity-5 group-hover:opacity-10 transition-opacity duration-300"></div>

    <!-- Animated border -->
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-r {{ $bg }} opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-[1px]">
        <div class="w-full h-full bg-white rounded-2xl"></div>
    </div>

    <div class="relative p-8">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-4">
                    <!-- Icon with enhanced styling -->
                    <div class="relative">
                        <div class="{{ $bg }} {{ $color }} p-4 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            {{ $slot }}
                        </div>
                        <!-- Glow effect -->
                        <div class="absolute inset-0 {{ $bg }} rounded-2xl blur-lg opacity-20 group-hover:opacity-40 transition-opacity duration-300"></div>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">{{ $title }}</h3>
                        <div class="flex items-baseline space-x-2">
                            <p class="text-3xl font-black text-gray-900 group-hover:scale-105 transition-transform duration-300">{{ number_format($value) }}</p>
                            @if($attributes->has('trend'))
                                <div class="flex items-center {{ $attributes['trend'] > 0 ? 'text-green-500' : 'text-red-500' }}">
                                    <svg class="w-4 h-4 {{ $attributes['trend'] > 0 ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span class="text-sm font-medium ml-1">{{ abs($attributes['trend']) }}%</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative element -->
            <div class="hidden lg:block">
                <div class="w-16 h-16 {{ $bg }} rounded-full opacity-10 group-hover:opacity-20 transition-opacity duration-300"></div>
            </div>
        </div>

        <!-- Progress bar for visual appeal -->
        @if($attributes->has('progress'))
            <div class="mt-6">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="{{ $bg }} h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $attributes['progress'] }}%"></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Hover effect overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
</div>