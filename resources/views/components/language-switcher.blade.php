<details class="relative inline-block text-left">
    <summary class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-700 hover:bg-gray-100 - focus:outline-none cursor-pointer" title="{{ __('Change Language') }}">
        
        <!-- NEW WORLD ICON -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9zm0 0c2.5 2.5 4 5.5 4 9s-1.5 6.5-4 9m0-18c-2.5 2.5-4 5.5-4 9s1.5 6.5 4 9m-9-9h18"/>
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