<?php

use App\Models\Prompt;
use Filament\Forms\Get;
use App\Models\Platform;
use Filament\Forms\Form;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;

new class extends Component implements HasForms {
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->helperText('A optional title for your prompt.')
                    ->rules(['string', 'min:7', 'max:155']),
                Select::make('platformId')
                    ->live()
                    ->required()
                    ->label('Platform')
                    ->options(
                        Platform::all()
                            ->mapWithKeys(fn (Platform $platform) => [$platform->id => $platform->name])
                            ->toArray()
                ),
                TextInput::make('tuner')
                    ->label('Tuner')
                    ->rules(['string', 'max:55'])
                    ->visible(function (Get $get): bool {
                        return Platform::select('id')
                            ->where('slug', 'midjourney')
                            ->get()
                            ->contains('id', $get('platformId'));
                    }),
                Textarea::make('prompt')
                    ->required()
                    ->rules(['string', 'max:1000', function () {
                        return function (string $attribute, mixed $value, Closure $fail) {
                            $newPrompt = trim($value);

                            $possibleExistingPrompt = Prompt::select('content')
                                ->where('user_id', auth()->id())
                                ->where('content', 'LIKE', "%{$newPrompt}%")
                                ->first();

                            if ($possibleExistingPrompt) {
                                $fail('You already have a prompt with this content');
                            }
                        };
                    }]),
                Textarea::make('negative_prompt')
                    ->rules(['string', 'max:500'])
                    ->visible(function (Get $get): bool {
                        return Platform::select('id')
                            ->where('slug', 'stable-diffusion')
                            ->orWhere('slug', 'leonardo')
                            ->get()
                            ->contains('id', $get('platformId'));
                    }),
                Grid::make('4')
                    ->schema([
                        TextInput::make('guidence_scale')
                            ->label('Guidence Scale')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->step(1),
                        TextInput::make('seed')
                            ->label('Seed')
                            ->rules(['string', 'max:55']),
                        TextInput::make('preset')
                            ->label('Preset')
                            ->rules(['string', 'max:55']),
                        TextInput::make('model')
                            ->label('Model')
                            ->rules(['string', 'max:55']),
                    ])
                    ->visible(function (Get $get): bool {
                        return Platform::select('id')
                            ->where('slug', 'stable-diffusion')
                            ->orWhere('slug', 'leonardo')
                            ->get()
                            ->contains('id', $get('platformId'));
                    }),
                FileUpload::make('images')
                    ->required()
                    ->multiple()
                    ->maxFiles(4)
                    ->rules([function () {
                        return function (string $attribute, mixed $value, Closure $fail) {
                            $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);

                            if (! in_array($extension, ['png', 'jpg', 'jpeg', 'gif'])) {
                                $fail('One or more images are not valid. Please upload a valid image. This can be sometimes caused by the filename having special characters. Please rename the file and try again.');
                            }
                        };
                    }]),
                Toggle::make('is_private')
                    ->label('Private')
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->helperText('Prevents the prompt from appearing on public lists.'),
                Toggle::make('agree')
                    ->required()
                    ->accepted()
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-o-x-mark')
                    ->helperText('You must agree to the terms of service.'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $additionalFields = array_filter([
            'guidence_scale' => $data['guidence_scale'] ?? null,
            'seed'           => $data['seed'] ?? null,
            'preset'         => $data['preset'] ?? null,
            'model'          => $data['model'] ?? null,
        ]);
        
        $promptData = [
            'user_id'          => auth()->id(),
            'title'            => $data['title'],
            'tuner'            => $data['tuner'] ?? null,
            'content'          => $data['prompt'],
            'negative_content' => $data['negative_prompt'] ?? null,
            'is_private'       => $data['is_private'],
        ];
        
        if ($additionalFields) {
            $promptData['additional_fields'] = $additionalFields;
        }
        
        $prompt = Prompt::create($promptData);

        foreach ($data['images'] as $image) {
            $filePath = Storage::disk('public')->path($image);
            $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

            $name = 'prompt-' . $prompt->ulid . '-' . auth()->user()->name;
            $fileName = $name . '.' . $fileExtension;

            $prompt
                ->addMedia($filePath)
                ->usingName($name)
                ->usingFileName($fileName)
                ->toMediaCollection('images');
        }

        $prompt->platforms()->attach($data['platformId']);

        $this->dispatch('close'); // Close modal
        $this->dispatch('notify', 'Prompt Created');
        $this->dispatch('prompt-created');

        $this->redirect($prompt->url(), navigate: true);
    }
}; ?>

<x-slot name="title">{{ __('Create Prompt') }}</x-slot>
<x-slot name="subheader">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Create Prompt') }}
    </h2>
</x-slot>

<div class="py-12 px-4 sm:gap-12 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden p-6 shadow-sm sm:rounded-lg">
            @if (auth()->check() && auth()->user()?->hasVerifiedEmail())
                <form wire:submit="save">
                    {{ $this->form }}
            
                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center gap-4">
                            <x-primary-button 
                                wire:loading.attr="disabled">
                                {{ __('Save') }}
                            </x-primary-button>
                
                            <x-action-message class="me-3" on="created-prompt">
                                {{ __('Saved.') }}
                            </x-action-message>
                        </div>
                    </div>
                </form>
            @elseif (auth()->check() && !auth()->user()?->hasVerifiedEmail())
                <div class="space-y-3">
                    <header>
                        <h2 class="text-lg text-center font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Please verify your email before creating prompt') }}
                        </h2>
                    </header>
        
                    <div class="flex justify-center">
                        <a class="mt-4" href="{{ route('verification.notice') }}" wire:navigate>
                            <x-primary-button type="button">{{ __('Verify Email') }}</x-primary-button>
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <header>
                        <h2 class="text-lg text-center font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Please login to create a prompt') }}
                        </h2>
                    </header>
        
                    <div class="gap-3 flex justify-center">
                        <a class="mt-4" href="{{ route('login') }}" wire:navigate>
                            <x-primary-button type="button">{{ __('Login') }}</x-primary-button>
                        </a>
        
                        @if (Route::has('register'))
                            <a class="mt-4" href="{{ route('register') }}" wire:navigate>
                                <x-primary-button type="button">{{ __('Register') }}</x-primary-button>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
