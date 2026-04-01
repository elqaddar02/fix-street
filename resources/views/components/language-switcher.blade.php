<details class="relative inline-block text-left">
    <summary class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none cursor-pointer" title="{{ __('Change Language') }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 2a8 8 0 00-6.928 4.5h13.856A8 8 0 0012 4zm-6.928 6.5A8.001 8.001 0 0112 20a8.001 8.001 0 01-6.928-9.5zm9.856 0H6.072a6.001 6.001 0 0011.856 0z" />
        </svg>
    </summary>

    <div class="absolute right-0 z-50 mt-2 w-48 rounded-lg shadow-lg bg-white dark:bg-gray-800">
        <div class="py-1">
            <a href="{{ route('language.switch', 'en') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 @if(app()->getLocale() == 'en') bg-gray-50 font-semibold dark:bg-gray-700 @endif">
                <span class="mr-3">🇬🇧</span>
                <span>English</span>
                @if(app()->getLocale() == 'en')
                    <svg class="w-4 h-4 ml-auto text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @endif
            </a>
            <a href="{{ route('language.switch', 'fr') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 @if(app()->getLocale() == 'fr') bg-gray-50 font-semibold dark:bg-gray-700 @endif">
                <span class="mr-3">🇫🇷</span>
                <span>Français</span>
                @if(app()->getLocale() == 'fr')
                    <svg class="w-4 h-4 ml-auto text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @endif
            </a>
            <a href="{{ route('language.switch', 'ar') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 @if(app()->getLocale() == 'ar') bg-gray-50 font-semibold dark:bg-gray-700 @endif">
                <span class="mr-3">🇸🇦</span>
                <span>العربية</span>
                @if(app()->getLocale() == 'ar')
                    <svg class="w-4 h-4 ml-auto text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @endif
            </a>
        </div>
    </div>
</details>
