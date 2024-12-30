<?php

use App\Models\Prompt;
use App\Models\Platform;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

new class extends Component {
    use WithFileUploads;

    #[Validate(['sometimes', 'string', 'min:7', 'max:155'])]
    public string $title = '';

    #[Validate(['sometimes', 'string', 'max:55'])]
    public string $tuner = '';

    // Handled in the rules method
    public string $prompt = '';

    #[Validate(['sometimes', 'string', 'max:500'])]
    public string $negative_prompt = '';

    #[Validate(['required', 'numeric'])]
    #[Validate('exists:platforms,id', message: 'A platform is required')]
    public int $platformId = 0;

    #[Validate(['sometimes', 'boolean'])]
    public bool $is_private = false;

    #[Validate(['required', 'boolean'])]
    #[Validate('accepted', message: 'You must agree to the terms of service')]
    public bool $agree = false;

    #[Validate(['required', 'array', 'max:4'])]
    public array $images = [];

    public bool $showTunerField = false;

    public bool $showNegitivePromptField = false;

    public function rules() 
    {
        return [
            'prompt' => [
                'required', 
                'string', 
                'max:1000',
                function (string $attribute, mixed $value, Closure $fail) {
                    $newPrompt = trim($value);

                    $possibleExistingPrompt = Prompt::select('content')
                        ->where('user_id', auth()->id())
                        ->where('content', 'LIKE', "%{$newPrompt}%")
                        ->first();

                    if ($possibleExistingPrompt) {
                        $fail('You already have a prompt with this content');
                    }
                }, 
            ],
        ];
    }

    public function updatedImages(array $value): void
    {
        $imagesFailedErrorMessages = 'The images field is required';

        foreach ($value as $image) {
            $extension = pathinfo($image->getFilename(), PATHINFO_EXTENSION);
            
            // There is a bug in livewire where it will not always validate the file type
            // so we have to do it manually and reset the images if it fails, plus create a custom error message
            if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $this->reset('images');
                
                $imagesFailedErrorMessages = 'One or more images are not valid. Please upload a valid image. This can be sometimes caused by the filename having special characters. Please rename the file and try again.';
            }
        }

        $this->validate(
            [
                'images'   => ['required', 'array', 'max:4'],
                'images.*' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:5000'], // 10MB Max
            ],
            [
                'images.required' => $imagesFailedErrorMessages,
                'images.*.max'    => 'The images may not be greater than 5MB.',
            ]
        );
    }

    public function updatedPlatformId(int $platformId): void
    {
        $this->showTunerField = Platform::select('id')
            ->where('slug', 'midjourney')
            ->get()
            ->contains('id', $platformId);

        $this->showNegitivePromptField = Platform::select('id')
            ->where('slug', 'stable-diffusion')
            ->orWhere('slug', 'leonardo')
            ->get()
            ->contains('id', $platformId);

    }

    public function with(): array
    {
        return [
            'platforms' => Platform::all(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        $prompt = Prompt::create([
            'user_id'          => auth()->id(),
            'title'            => $this->title,
            'tuner'            => $this->tuner,
            'content'          => $this->prompt,
            'negative_content' => $this->negative_prompt,
            'is_private'       => $this->is_private,
        ]);

        foreach ($this->images as $image) {
            $name = 'prompt-' . $prompt->ulid . '-' . auth()->user()->name;
            $fileName = $name . '.' . $image->extension();

            $prompt
                ->addMediaFromUrl($image->temporaryUrl())
                ->usingName($name)
                ->usingFileName($fileName)
                ->toMediaCollection('images');
        }

        $prompt->platforms()->attach($this->platformId);

        $this->dispatch('close'); // Close modal
        $this->dispatch('notify', 'Prompt Created');
        $this->dispatch('prompt-created');

        $this->redirect($prompt->url(), navigate: true);
    }
}; ?>

<section>
    @if (auth()->check() && auth()->user()?->hasVerifiedEmail())
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Create Prompt') }}
            </h2>
        </header>

        <form wire:submit="save" x-data class="mt-6 space-y-6">
            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input wire:model.live="title" id="title" name="title" type="text" class="mt-1 block w-full" />
                <small class="text-gray-600 dark:text-gray-400">Character count: <span x-text="$wire.title.length"></span></small>
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="platform" :value="__('Platform*')" />
                <x-select wire:model.live="platformId" id="platform" name="select" class="mt-1 block w-full" required>
                    <option value="0">Select</option>
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error class="mt-2" :messages="$errors->get('platformId')" />
            </div>

            @if ($showTunerField)
                <div>
                    <x-input-label for="tuner" :value="__('Tuner')" />
                    <x-text-input wire:model.live="tuner" id="tuner" name="tuner" type="text" class="mt-1 block w-full" />
                    <small class="text-gray-600 dark:text-gray-400">Character count: <span x-text="$wire.tuner.length"></span></small>
                    <x-input-error class="mt-2" :messages="$errors->get('tuner')" />
                </div>
            @endif

            <div>
                <x-input-label for="prompt" :value="__('Prompt*')" />
                <x-text-area wire:model.live="prompt" id="prompt" name="prompt" class="mt-1 block w-full" rows="3" required />
                <small class="text-gray-600 dark:text-gray-400">Character count: <span x-text="$wire.prompt.length"></span></small>
                <x-input-error class="mt-2" :messages="$errors->get('prompt')" />
            </div>

            @if ($showNegitivePromptField)
                <div>
                    <x-input-label for="negative-prompt" :value="__('Negative Prompt')" />
                    <x-text-area wire:model.live="negative_prompt" id="negative-prompt" name="negative_prompt" class="mt-1 block w-full" rows="2" />
                    <small class="text-gray-600 dark:text-gray-400">Character count: <span x-text="$wire.negative_prompt.length"></span></small>
                    <x-input-error class="mt-2" :messages="$errors->get('negative_prompt')" />
                </div>
            @endif

            <div
                x-data="{ 
                    uploading: true, 
                    progress: 0
                }"
                x-on:livewire-upload-start="uploading = true"
                x-on:livewire-upload-finish="uploading = false"
                x-on:livewire-upload-error="uploading = false"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
            >
                <!-- File Input -->
                <div>
                    <x-input-label for="image" :value="__('Images*')" />
                    <x-text-input wire:model.live="images" id="image" name="image" type="file" accept="image/jpg,image/jpeg,image/png" class="mt-1 p-1 block w-full" required multiple />
                    <small class="text-gray-600 dark:text-gray-400">Only upload prompt generated images</small>
                    <x-input-error class="mt-2" :messages="$errors->get('images')" />
                        
                    @if ($errors->has('images.*'))
                        @foreach ($errors->get('images.*') as $_error)
                            <x-input-error class="mt-2" :messages="$_error" />
                        @endforeach
                    @endif
                </div>
        
                <!-- Progress Bar -->
                <div x-show="uploading">
                    <x-progress></x-progress>
                </div>
            </div>

            @if ($images && count($images) <= 4)
                <div class="gap-4 flex flex-wrap">
                    @foreach ($images as $image)
                        <img class="h-20 object-cover rounded-lg shadow-sm w-20" src="{{ $image->temporaryUrl() }}">
                    @endforeach
                </div>
            @endif

            <div>
                <div class="flex items-center space-x-2">
                    <x-text-input wire:model.live="is_private" id="is_private" name="is_private" type="checkbox" />
                    <x-input-label for="is_private" :value="__('Private')" />
                </div>
                <small class="text-gray-600 dark:text-gray-400">Prevents prompt from appearing on public lists. The individual prompt page will still be accessible.</small>
                <x-input-error class="mt-2" :messages="$errors->get('is_private')" />
            </div>

            <div>
                <div class="flex items-center space-x-2">
                    <x-text-input wire:model.live="agree" id="agree" name="agree" type="checkbox" required />
                    <x-input-label class="shrink-0" for="agree" :value="__('I Agree')" />
                </div>
                <small class="text-gray-600 dark:text-gray-400">
                    By checking this box, I acknowledge that I have read, understood, and agree to be bound by the <a class="underline hover:opacity-60" href="{{ route('pages.terms-of-service') }}" target="_blank">Terms of Service</a>. Failure to comply with these terms may result in account suspension or termination.
                </small>
                <x-input-error class="mt-2" :messages="$errors->get('agree')" />
            </div>

            <div class="flex items-center justify-between">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <span class="text-sm text-gray-600 dark:text-gray-400">* required</span>
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
</section>
