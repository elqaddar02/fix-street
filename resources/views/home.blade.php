@php($title = __('Madinup - Independent Citizen Platform for Street Problems'))

<x-app-layout>
    @include('home.styles')

    <!-- Hero Section -->
    @include('home.hero-section')

    <!-- Ad Banner -->
    <section class="bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
            <x-ad-banner type="horizontal" />
        </div>
    </section>

    <!-- Categories Section -->
    @include('home.categories-section')

    <!-- Recent Reports Section -->
    @include('home.latest-reports-section')

    <!-- How It Works Section -->
    @include('home.how-it-works-section')

    <!-- CTA Section -->
    @include('home.cta-section')

    @include('home.scripts')
</x-app-layout>
