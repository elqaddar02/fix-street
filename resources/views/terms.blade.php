@php($title = 'Terms of Service - Madinova')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Terms of Service
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-red-200 shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @include('termscontent')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
