<?php

use App\Models\Prompt;
use Filament\Forms\Form;
use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

new class extends Component implements HasForms {
    use InteractsWithForms;

    #[Locked]
    public Prompt $prompt;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'title'      => $this->prompt->title,
            'is_private' => $this->prompt->is_private,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->helperText('A optional title for your prompt.')
                    ->rules(['min:7', 'max:155']),
                Toggle::make('is_private')
                    ->label('Private')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->helperText('Prevents the prompt from appearing on public lists.'),
            ])
            ->statePath('data');
    }

    public function update(): void
    {
        $this->authorize('update', $this->prompt);

        $this->prompt->update($this->form->getState());

        $this->dispatch('updated-prompt');
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->prompt);

        $this->prompt->delete();

        $this->dispatch('notify', 'Prompt Deleted');
        $this->dispatch('close-panel');

        $this->redirect($this->prompt->user->archiveUrl(), navigate: true);
    }
}; ?>

<div class="absolute bg-white dark:bg-gray-800 duration-150 h-full p-6 max-w-96 right-0 shadow-lg top-0 w-full z-10 sm:rounded-lg">
    @can(['update', 'delete'], $prompt)
        <button 
            @click="open = !open" 
            :class="{ 'opacity-100 pointer-events-auto': open }"
            class="absolute bg-gray-100 dark:bg-gray-900 duration-150 flex font-medium items-center left-0 opacity-0 pointer-events-none px-3 py-1 space-x-1.5 text-gray-800 dark:text-gray-200 text-sm -top-3.5 hover:bg-gray-200 hover:dark:bg-gray-700 sm:rounded-br-lg sm:rounded-tl-lg md:top-0">
                <span>Close</span>
                <svg :class="{ 'rotate-180': open }" class="h-3.5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" height="16" width="14" viewBox="0 0 448 512"><path d="M445.5 261.8c1.6-1.5 2.5-3.6 2.5-5.8s-.9-4.3-2.5-5.8l-192-184c-3.2-3.1-8.3-2.9-11.3 .2s-2.9 8.3 .2 11.3L420.1 248 8 248c-4.4 0-8 3.6-8 8s3.6 8 8 8l412.1 0L242.5 434.2c-3.2 3.1-3.3 8.1-.2 11.3s8.1 3.3 11.3 .2l192-184z"/></svg>
        </button>
    @endcan
    <div class="mt-4 sticky text-gray-900 dark:text-gray-100 top-28 md:mt-8">
        <header>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit') }}
            </h3>
        </header>
        <form wire:submit="update" class="mt-2.5">
            {{ $this->form }}
    
            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center gap-4">
                    <x-primary-button 
                        wire:target="delete"
                        wire:loading.attr="disabled">
                        {{ __('Update') }}
                    </x-primary-button>
        
                    <x-action-message class="me-3" on="updated-prompt">
                        {{ __('Updated.') }}
                    </x-action-message>
                </div>
            </div>
        </form>

        <div class="border-t border-gray-100 dark:border-gray-900 mt-3 pt-6">
            <x-danger-button
                wire:click="delete"
                wire:confirm="Are you sure you want to delete this prompt?"
                type="button">
                {{ __('Delete Prompt') }}
            </x-danger-button>
        </div>
    </div>
</div>
