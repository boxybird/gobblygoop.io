<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Primary Meta Tags -->
        @isset($meta)
            {{ $meta }}
        @else
            <title>{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name')  }}</title>
            <meta name="description" content="{{ isset($description) ? $description . ' | ' . config('app.description') : config('app.description')  }}">
        @endisset

        <!-- Open Graph / Facebook -->
        @isset($openGraph)
            {{ $openGraph }}
        @else
            <meta property="og:type" content="website">
            <meta property="og:url" content="{{ url()->current() }}">
            <meta property="og:title" content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name')  }}">
            <meta property="og:description" content="{{ isset($description) ? $description : config('app.description') }}">
            <meta property="og:image" content="{{ isset($image) ? $image : asset('img/logo.png') }}">
        @endisset

        <!-- Twitter -->
        @isset($twitter)
            {{ $twitter }}
        @else
            <meta property="twitter:card" content="summary_large_image">
            <meta property="twitter:url" content="{{ url()->current() }}">
            <meta property="twitter:title" content="{{ isset($title) ? $title . ' | ' . config('app.name') : config('app.name')  }}">
            <meta property="twitter:description" content="{{ isset($description) ? $description : config('app.description') }}">
            <meta property="twitter:image" content="{{ isset($image) ? $image : asset('img/logo.png') }}">
        @endisset

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
        @vite('resources/js/app.js')

        <!-- Styles -->
        @filamentStyles
        @vite('resources/css/app.css')

        @if (config('app.analytics'))
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-VLRBWNBPDL"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', 'G-VLRBWNBPDL');
            </script>
        @endif
    </head>
    <body
        x-data
        :class="{ 'dark': $store.darkMode.on }"
        x-cloak
        class="font-sans antialiased overflow-y-scroll">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Site Notice -->
            <div class="bg-yellow-100">
                <p class="flex items-center justify-center max-w-7xl mx-auto py-1 space-x-2 to-yellow-900">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="16" viewBox="0 0 512 512"><path d="M256 16a240 240 0 1 1 0 480 240 240 0 1 1 0-480zm0 496A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM208 352c-4.4 0-8 3.6-8 8s3.6 8 8 8h96c4.4 0 8-3.6 8-8s-3.6-8-8-8H264V216c0-4.4-3.6-8-8-8H224c-4.4 0-8 3.6-8 8s3.6 8 8 8h24V352H208zm48-176a16 16 0 1 0 0-32 16 16 0 1 0 0 32z"/></svg>
                    <span>We are currently in beta</span>
                </p>
            </div>

            <livewire:layout.navigation />

            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-800 shadow sticky top-0 z-20">
                <div class="gap-6 flex items-center justify-between max-w-7xl mx-auto py-6 px-4 sm:gap-12 sm:px-6 lg:px-8">
                    <a href="/" wire:navigate>
                        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            {{ config('app.name', 'Laravel') }}
                        </h2>
                    </a>

                    @if (Route::is('welcome'))
                        <livewire:layout.search-bar />
                    @endif

                    <a href="{{ route('prompts.create') }}" wire:navigate>
                        <x-primary-button>
                            {{ __('Create Prompt') }}
                        </x-primary-button>
                    </a>
                </div>
            </header>

            <!-- Page Subheader -->
            @if (isset($subheader))
                <div class="bg-white dark:bg-gray-800 border-t dark:border-gray-700 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $subheader }}
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="min-h-svh">
                {{ $slot }}
            </main>

            <footer class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col justify-between space-y-2 text-sm text-gray-500 dark:text-gray-400 sm:flex-row sm:space-y-0">
                        <div>
                            <p>
                                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                            </p>
                        </div>
                        <nav class="space-x-4">
                            <a wire:navigate href="{{ route('pages.privacy-policy') }}">
                                {{ __('Privacy Policy') }}
                            </a>
                            <a wire:navigate href="{{ route('pages.terms-of-service') }}">
                                {{ __('Terms of Service') }}
                            </a>
                            <a wire:navigate href="{{ route('pages.about-us') }}">
                                {{ __('About Us') }}
                            </a>
                        </nav>
                    </div>
                </div>
            </footer>
        </div>

        @persist('toast')
            <x-toast />
        @endpersist

        <x-modal name="create-prompt" focusable>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:prompts.create />
                </div>
            </div>
        </x-modal>

        @filamentScripts
    </body>
</html>
