<?php

use App\Models\User;
use App\Models\Prompt;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public Prompt $prompt;
    
    protected $listeners = [
        'updated-prompt' => '$refresh',
    ];

    public function with(): array 
    {
        $id = $this->prompt->id;
        $user = $this->prompt->user->name;
        $platform = $this->prompt->platforms->first()?->name;
        $excerpt = str()->of($this->prompt->content)->squish()->words(25);
        $title = $this->prompt->title
            ? str($this->prompt->title)->apa()
            : "A {$platform} prompt by {$user}" . ' | ' . config('app.name');

        return [
            'meta' => [
                'title'       => $title . ' | ' . config('app.name'),
                'description' => "Prompt: {$excerpt}",
                'image'       => $this->prompt->imageUrl(),
                'url'         => $this->prompt->url(),
            ]
        ];
    }
}; ?>

<x-slot:meta>
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="image" content="{{ $meta['image'] }}">
</x-slot>

<x-slot:open-graph>
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $meta['url'] }}">
    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:image" content="{{ $meta['image'] }}">
</x-slot>

<x-slot:twitter>
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $meta['url'] }}">
    <meta property="twitter:title" content="{{ $meta['title'] }}">
    <meta property="twitter:description" content="{{ $meta['description'] }}">
    <meta property="twitter:image" content="{{ $meta['image'] }}">
</x-slot>

<div
    x-data="{
        open: false,
    }"
    @keydown.escape="open = false"
    @close-panel.window="open = false">
    <div
        class="py-12 px-4 sm:gap-12 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div
                @click.away="open = false" 
                class="isolate relative">
                <div
                    :class="{ 'translate-x-0': !open, '-translate-x-[25.5rem]': open }"  
                    class="bg-white dark:bg-gray-800 duration-150 shadow-sm relative z-20 sm:rounded-lg">
                    @can(['update', 'delete'], $prompt)
                        <button 
                            @click="open = !open"
                            :class="{ '!opacity-0 pointer-events-none': open }"
                            class="absolute bg-gray-100 dark:bg-gray-900 duration-150 flex font-medium items-center opacity-100 px-3 py-1 right-0 space-x-1.5 text-gray-800 dark:text-gray-200 text-sm -top-3.5 hover:bg-gray-200 hover:dark:bg-gray-700 sm:rounded-bl-lg sm:rounded-tr-lg md:top-0">
                                <span>Edit</span>
                                <svg :class="{ 'rotate-180': open }" class="h-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="14" viewBox="0 0 448 512"><path d="M445.5 261.8c1.6-1.5 2.5-3.6 2.5-5.8s-.9-4.3-2.5-5.8l-192-184c-3.2-3.1-8.3-2.9-11.3 .2s-2.9 8.3 .2 11.3L420.1 248 8 248c-4.4 0-8 3.6-8 8s3.6 8 8 8l412.1 0L242.5 434.2c-3.2 3.1-3.3 8.1-.2 11.3s8.1 3.3 11.3 .2l192-184z"/></svg>
                        </button>
                    @endcan
                    <div class="p-6 text-gray-900 dark:text-gray-100">                        
                        <article class="gap-12 grid grid-cols-1 md:grid-cols-[50%_1fr]">
                            <div class="gap-6 grid">
                                @foreach ($prompt->imageUrls() as $image)
                                    <img
                                        x-data="{
                                            open() {
                                                $dispatch('open-modal', 'image-{{ $loop->index }}')
                                            }
                                        }"
                                        @click="open"
                                        class="cursor-pointer w-full sm:rounded-lg" 
                                        src="{{ $image['medium'] }}"
                                        alt="image for prompt"
                                        loading="lazy">
                                @endforeach
                            </div>
                            <div>
                                <div class="sticky top-28">
                                    <div>
                                        @if ($prompt->title)
                                            <h1 class="border-b border-gray-100 dark:border-gray-900 font-bold leading-relaxed mt-6 pb-2 text-2xl text-gray-900 dark:text-gray-100">
                                                {{ str($prompt->title)->apa() }}
                                            </h1>
                                            <h2 class="text-lg font-medium mt-5 text-gray-800 dark:text-gray-200">
                                                {{ __('Details') }}
                                            </h2>
                                        @else
                                            <h1 class="font-medium mt-6 text-lg text-gray-800 dark:text-gray-200">
                                                {{ __('Details') }}
                                            </h1>
                                        @endif
                                    </div>

                                    <div class="mt-2.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Prompt</span>
                                        <p 
                                            x-ref="prompt"
                                            class="text-gray-700 dark:text-gray-300">
                                            {!! nl2br(e($prompt->content)) !!}
                                        </p>
                                    </div>

                                    @if ($prompt->negative_content)
                                        <div class="mt-2.5">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Negative Prompt</span>
                                            <p class="text-gray-700 dark:text-gray-300">
                                                {!! nl2br(e($prompt->negative_content)) !!}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($prompt->additional_fields->isNotEmpty())
                                        <div class="mt-2.5">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Additional Fields</span>
                                            <ul class="text-gray-700 dark:text-gray-300 text-sm">
                                                @foreach ($prompt->additional_fields as $field)
                                                    <li>
                                                        <span class="font-medium inline-block sm:min-w-32">{{ $field['key'] }}:</span> {{ $field['value'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if ($prompt->tuner)
                                        <div class="mt-2.5">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Tuner</span>
                                            <code
                                                class="bg-white/60 dark:bg-gray-900/60 break-all block px-1.5 py-0.5 rounded text-gray-700 dark:text-gray-300">
                                                {{ $prompt->tuner }}
                                            </code>
                                        </div>
                                    @endif

                                    <div class="mt-2.5">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Platform</span>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            {{ $prompt->platforms->first()?->name ?? '' }}
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
                                            type="button">
                                            {{ __('Copy Prompt') }}
                                        </x-primary-button>
                                        <x-secondary-button
                                            x-data
                                            @click="$dispatch('open-modal', 'share')"
                                            type="button">
                                            {{ __('Launch') }}
                                        </x-secondary-button>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <aside class="border-t border-gray-100 dark:border-gray-900 mt-6 pt-4">
                            <div class="overflow-hidden sm:rounded-lg">
                                <x-user-details-box :user="$prompt->user" class="-mb-4" />
                                <livewire:prompts.prompt-grid :user="$prompt->user" :exclude-ids="[$prompt->id]">
                            </div>
                        </aside>
                    </div>
                </div>
                @can(['update', 'delete'], $prompt)
                    <livewire:prompts.edit-panel :$prompt />
                @endcan
            </div>
        </div>

        @foreach ($prompt->imageUrls() as $image)
            <x-modal name="image-{{ $loop->index }}" max-width="5xl">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <img class="w-full sm:rounded-lg" src="{{ $image['large'] }}">
                    </div>
                </div>
            </x-modal>
        @endforeach

        <x-modal name="share" focusable>
            <x-share-prompt url="{{ $prompt->url() }}" content="{{ $prompt->content }}" />
        </x-modal>
    </div>
</div>
