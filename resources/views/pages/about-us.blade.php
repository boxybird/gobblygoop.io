<x-app-layout>
    <x-slot name="title">{{ __('About Us') }}</x-slot>
    <x-slot name="subheader">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('About Us') }}
        </h2>
    </x-slot>
    <div class="py-12 px-4 sm:gap-12 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden prose dark:prose-invert sm:rounded-lg">
                {!! Str::markdown(file_get_contents(resource_path('views/pages/markdown/about-us.md'))) !!}
            </div>
        </div>
    </div>
</x-app-layout>