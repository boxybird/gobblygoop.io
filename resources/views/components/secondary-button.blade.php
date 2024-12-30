@php
    $classes = 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:animate-pulse disabled:cursor-wait disabled:opacity-70 transition ease-in-out duration-150';
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }} 
    </a>
@else
    <button {{ $attributes->merge(['type' => 'sumbit', 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif














