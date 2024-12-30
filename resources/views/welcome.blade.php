<x-app-layout>
    <div class="py-12 px-4 relative sm:gap-12 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div @class(['bg-gray-100 dark:bg-gray-900 overflow-hidden relative sm:rounded-lg', 'blur-[2px]' => auth()->guest()])>
                <livewire:prompts.prompt-grid limit="{{ auth()->guest() ? 6 : 0 }}" />
            </div>
        </div>

        @guest
            <div class="absolute inset-0 min-h-svh">
                <div class="sticky top-56 bg-gradient-to-br from-white to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-lg overflow-hidden shadow-xl max-w-[87%] mx-auto p-12 text-gray-900 dark:text-gray-100 text-center transform transition-all z-50 sm:max-w-xl">
                    <h2 class="text-3xl font-bold">
                        {{ config('app.name') }}
                    </h2>
                    <p class="mt-4 opacity-80 text-xl">{{ __('The Place to Save, Share, and Explore AI Prompts') }}</p>
                    <a class="block mt-10" href="{{ route('register') }}" wire:navigate>
                        <x-primary-button type="button">{{ __('Create an Account') }}</x-primary-button>
                    </a>
                    <p class="mt-4 opacity-80 text-sm">{{ __('Sign-up is free, Your AI memories will last a lifetime') }}</p>
                </div>
                <livewire:background-squares lazy="on-load" />
                <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-300 dark:from-gray-600 dark:to-gray-800 opacity-75"></div>
            </div>
        @endguest
    </div>
</x-app-layout>