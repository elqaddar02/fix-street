@php($title = __('Madinova - Official City Street Maintenance Portal'))

<x-app-layout>
    @include('home.styles')

    <!-- Hero Section -->
    @include('home.hero-section')

    <!-- Ad Banner -->
    <section class="max-w-7xl mx-auto px-6 py-8">
        <x-ad-banner type="horizontal" />
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
