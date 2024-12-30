<?php

use App\Models\User;
use App\Models\Prompt;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    #[Locked]
    public ?User $user = null;

    public string $q = '';

    public ?int $platform = null;
    
    public int $perPage = 24;

    public bool $showPrivate = false;

    public array $excludeIds = [];

    public bool $filterByLikes = false;

    public int $limit = 0;

    #[On('prompt-created')]
    #[On('search-bar-submitted')]
    public function mount($params = [])
    {
        $this->q = !empty(request()->get('q')) 
            ? request()->get('q') 
            : $params['q'] ?? '';

        $this->platform = !empty(request()->get('platform'))
            ? request()->get('platform')
            : $params['platform'] ?? null;
    }

    public function loadMore()
    {
        $this->perPage += 24;
    }

    public function with()
    {
        $prompts = Prompt::with(['user', 'platforms', 'media'])
            ->when(!$this->showPrivate, function ($query) {
                $query->where(function ($query) {
                    $query->where('is_private', false)
                        ->orWhere('user_id', auth()->id());
                });
            })
            ->when(!$this->user && $this->q, function ($query) {
                $query->where('content', 'like', "%{$this->q}%");
            })
            ->when(!$this->user && $this->platform, function ($query) {
                $query->whereHas('platforms', function ($query) {
                    $query->where('platform_id', $this->platform);
                });
            })
            ->when($this->user && !$this->filterByLikes, function ($query) {
                $query->where('user_id', $this->user->id);
            })
            ->when($this->filterByLikes, function ($query) {
                $query->whereHas('likers', function ($query) {
                    $query->where('user_id', $this->user->id);
                });
            })
            ->when($this->excludeIds, function ($query) {
                $query->whereNotIn('id', $this->excludeIds);
            })
            ->latest()
            ->paginate($this->limit ?? $this->perPage);

        return [
            'prompts' => $prompts,
        ];
    }
}; ?>

<div class="relative">
    @if ($prompts->isNotEmpty())
        <div class="hidden items-center justify-end xxs:flex">
            <x-grid-mode-toggle />
        </div>

        <div class="gap-1 grid grid-cols-1 mt-8 overflow-hidden xxs:mt-4 sm:grid-cols-2 sm:rounded-lg lg:grid-cols-3 xl:grid-cols-8">
            @foreach ($prompts as $prompt)
                @php
                    $span = $loop->index % 5 === 0 ? 'xl:col-span-4' : 'xl:col-span-2';
                @endphp

                <livewire:prompts.prompt-card :prompt="$prompt" :span="$span" :key="$prompt->id" />
            @endforeach
        </div>

        @if ($prompts->hasMorePages() && !$limit)
            <div x-intersect="$wire.loadMore"></div>
        @endif

        <div class="w-full" wire:loading>
            <p class="animate-pulse mt-12 text-gray-500 dark:text-gray-400 text-center">Loading</p>
        </div>
    @else
        <div class="mt-6 p-6 text-center">
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('No prompts found') }}
            </p>
        </div>
    @endif
</div>
