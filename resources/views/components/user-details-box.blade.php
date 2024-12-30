@props(['user', 'title' => null])

<div {{ $attributes->merge(['class' => 'flex items-end max-w-max relative space-x-4 z-10 sm:space-x-5 sm:items-center']) }}>
    @if ($user->avatarUrl('small'))
        <img class="h-16 object-cover w-16 sm:h-20 sm:rounded-md sm:w-20" src="{{ $user->avatarUrl('thumb') ?? '' }}" alt="avatar for {{ $user->name }}">
    @else
        <div class="bg-gray-300 dark:bg-gray-700 grid h-16 place-content-center w-16 sm:h-20 sm:rounded-lg sm:w-20">
            <span class="font-semibold text-xl">{{ $user->initial() }}</span>
        </div>
    @endif
    <div>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @if ($title)
                {{ $title }}
            @else
                {{ __('Prompts by') }} {{ $user->name }}
            @endif
        </h2>
        <div class="border-t dark:border-gray-700 flex items-end mt-1.5 pt-2.5 space-x-5 sm:justify-end">
            @if ($user->linkedin || $user->twitter)
                <span class="text-xs text-gray-600 dark:text-gray-400">Socials</span>
                <div class="flex items-baseline space-x-3">
                    @if ($user->linkedin)
                        <a class="duration-150 hover:opacity-80" href="{{ $user->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="Link to Linkedin">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-600 dark:text-gray-400" fill="currentColor" height="16" width="14" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M100.3 448H7.4V148.9h92.9zM53.8 108.1C24.1 108.1 0 83.5 0 53.8a53.8 53.8 0 0 1 107.6 0c0 29.7-24.1 54.3-53.8 54.3zM447.9 448h-92.7V302.4c0-34.7-.7-79.2-48.3-79.2-48.3 0-55.7 37.7-55.7 76.7V448h-92.8V148.9h89.1v40.8h1.3c12.4-23.5 42.7-48.3 87.9-48.3 94 0 111.3 61.9 111.3 142.3V448z"/></svg>
                        </a>
                    @endif
                    @if ($user->twitter)
                        <a class="duration-150 hover:opacity-80" href="{{ $user->twitter }}" target="_blank" rel="noopener noreferrer" aria-label="Link to Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 text-gray-600 dark:text-gray-400" fill="currentColor" height="16" width="16" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                        </a>
                    @endif
                </div>
            @else
                <span class="text-xs text-gray-600 dark:text-gray-400">No Socials</span>
            @endif
        </div>
    </div>
</div>