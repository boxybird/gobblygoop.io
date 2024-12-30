@props(['href'])

<a href="{{ $href }}" class="gap-2 flex items-center" target="_blank" rel="noopener noreferrer">
    <span class="[&_svg]:h-6 [&_svg]:w-6 [&_svg]:text-gray-800 [&_svg]:dark:text-gray-200">
        {{ $icon }}
    </span>
    <span class="text-sm text-gray-500 [&_svg]:dark:text-gray-400">
        {{ $slot }}
    </span>
</a>
