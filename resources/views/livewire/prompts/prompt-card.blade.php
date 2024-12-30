<?php

use App\Models\Prompt;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public Prompt $prompt;
    
    public string $span = '';

    public function with(): array
    {   
        $imageUrls = $this->prompt->imageUrls();

        return [
            'randomImage' => $imageUrls->isNotEmpty()
                ? $imageUrls->random()['medium']
                : null
        ];   
    }
}; ?>

<article class="group overflow-clip relative {{ $span }}">
    <a href="{{ $prompt->url() }}" wire:navigate>
        @if ($randomImage)
            <img
                class="absolute duration-300 h-full inset-0 w-full object-cover top-0 group-hover:scale-103"
                src="{{ $randomImage }}" 
                alt="image for prompt"
                loading="lazy">
        @else
            <div class="absolute duration-300 h-full inset-0 w-full bg-white dark:bg-gray-800 top-0"></div>  
        @endif
    </a>
    <div 
        :class="{ '!invisible !opacity-0': $store.gridMode.on }"
        class="duration-500 relative h-full p-6 pt-56 bg-gradient-to-b opacity-100 visible from-white/20 dark:from-gray-800/20 to-white/95 dark:to-gray-900/95 via-white/90 dark:via-gray-900/90 flex flex-col justify-end transition-all">
        <div class="flex items-center justify-between">
            <header>
                <a class="duration-150 flex items-center space-x-2 hover:opacity-80" href="{{ route('prompts.index', ['user' => $prompt->user]) }}" wire:navigate>
                    <h2 class="font-medium text-gray-900 dark:text-white">
                        {{ $prompt->user->name }} 
                    </h2>
                    <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-900 dark:text-white" fill="currentColor" height="16" width="18" viewBox="0 0 576 512"><path d="M32 256c0-88.4 71.6-160 160-160h56 8v16h-8H192c-79.5 0-144 64.5-144 144s64.5 144 144 144h56 8v16h-8H192c-88.4 0-160-71.6-160-160zm512 0c0 88.4-71.6 160-160 160H328h-8V400h8 56c79.5 0 144-64.5 144-144s-64.5-144-144-144H328h-8V96h8 56c88.4 0 160 71.6 160 160zm-400-8H432h8v16h-8H144h-8V248h8z"/></svg>
                </a>
            </header>
            <livewire:prompts.prompt-like :$prompt />
        </div>

        <div class="mt-2.5">
            <span class="text-xs text-gray-500 dark:text-gray-400">Prompt</span>
            <p 
                x-ref="prompt"
                class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                {{ $prompt->content }}
            </p>
        </div>

        @if ($prompt->tuner)
            <div class="mt-2.5">
                <span class="text-xs text-gray-500 dark:text-gray-400">Tuner</span>
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $prompt->tuner }}
                </p>
            </div>
        @endif

        <div class="mt-2.5">
            <span class="text-xs text-gray-500 dark:text-gray-400">Platform</span>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ $prompt->platforms->first()?->name }}
            </p>
        </div>

        <div class="mt-6 space-x-1.5">
            <x-primary-button 
                x-data="{
                    copy() {
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText($refs.prompt.innerText)
                                .then(() => $dispatch('notify', 'Prompt Copied'))
                                .catch(() => alert('Failed to copy URL! Please try again later.'))
                        } else {
                            alert('Failed to copy URL! Please try again later.')
                        }
                    }
                }"
                @click="copy"
                class="!bg-opacity-90 !py-1"
                type="button">
                {{ __('Copy Prompt') }}
            </x-primary-button>
            <x-secondary-button
                wire:navigate
                href="{{ $prompt->url() }}"
                class="!bg-opacity-90 !py-1">
                {{ __('View') }}
            </x-secondary-button>
        </div>
    </div>
</article>