<?php

use App\Models\Platform;
use Livewire\Volt\Component;
use Livewire\Attributes\Url;

new class extends Component
{
    #[Url]
    public string $q = '';

    #[Url]
    public int $platform = 0;

    public function updated()
    {
        $this->dispatch('search-bar-submitted', $this->params());
    }

    public function submit(): void
    {
        $this->dispatch('search-bar-submitted', $this->params());
    }

    protected function params(): array
    {
        return [
            'q'        => $this->q,
            'platform' => $this->platform,
        ];
    }

    public function with(): array
    {
        return [
            'platforms' => Platform::all(),
        ];
    }
}; ?>

<form
    wire:submit="submit"
    class="flex-1 hidden lg:flex lg:items-center">
    <div class="flex-1">
        <x-input-label class="sr-only" for="search-bar" :value="__('Search')" />
        <x-text-input wire:model.live="q" class="py-1 rounded-r-none w-full" id="search-bar" name="q" type="search" :placeholder="__('Where your journey begins...')" />
    </div>
    <div>
        <x-input-label class="sr-only" for="platform" :value="__('Platform')" />
        <x-select wire:model.live="platform" id="platform" class="py-1 rounded-none">
            <option>{{ __('All Platforms') }}</option>
            @foreach ($platforms as $platform)
                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
            @endforeach
        </x-select>
    </div>
    <x-primary-button class="rounded-l-none">{{ __('Go') }}</x-primary-button>
</form>