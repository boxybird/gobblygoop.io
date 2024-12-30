<?php

use App\Models\Prompt;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public Prompt $prompt;
    
    public function like()
    {
        if (auth()->guest()) {
            $this->dispatch('notify', 'You must be logged in to like a prompt.');
            
            return;
        }

        auth()->user()->toggleLike($this->prompt);
    }
}; ?>

<button
    wire:click="like"
    wire:loading.class="animate-pulse"
    class="flex group/like items-center space-x-1.5"
    type="button">
    <span class="bg-gray-300 dark:bg-gray-500 duration-300 opacity-0 px-2.5 py-0 rounded-md tabular-nums text-gray-900 dark:text-gray-100 text-xs group-hover/like:opacity-100">
        {{ $prompt->likers()->count() }}
    </span>

    @if (auth()->user()?->hasLiked($prompt))
        <span class="sr-only">Unlike</span>
        <svg class="text-red-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="16" viewBox="0 0 512 512"><path d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/></svg>        
    @else
        <span class="sr-only">Like</span>
        <svg class="text-red-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="16" viewBox="0 0 512 512"><path d="M393.6 57.7c-42.9-5.6-85.8 12.6-111.2 47.6l-20 27.5c-1.5 2.1-3.9 3.3-6.5 3.3s-5-1.2-6.5-3.3l-20-27.5c-25.4-34.9-68.3-53.2-111.2-47.6C60.1 65.3 16 114.9 16 173.5c0 30.3 11.8 59.4 32.9 81.2L248 460.4c2.2 2.3 5.2 3.6 8.4 3.6s6.2-1.3 8.4-3.6L463.2 254.7c21-21.8 32.8-50.9 32.8-81.1C496 115 451.9 65.3 393.6 57.7zM269.5 95.8c28.9-39.7 77.6-60.4 126.2-54c66.1 8.6 116.3 65 116.3 131.8c0 34.4-13.4 67.5-37.2 92.2L276.2 471.5c-5.2 5.4-12.4 8.5-19.9 8.5c-7.5 0-14.7-3-19.9-8.4L37.4 265.8C13.4 241.1 0 208 0 173.5C0 106.8 50.2 50.4 116.3 41.8c48.6-6.3 97.4 14.3 126.2 54L256 114.4l13.5-18.6z"/></svg>
    @endif
</button>
