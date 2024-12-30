<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Symfony\Component\Uid\Ulid;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Providers\RouteServiceProvider;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    
    public string $email = '';
    
    public string $twitter = '';
    
    public string $linkedin = '';
    
    public ?TemporaryUploadedFile $avatar = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->twitter = Auth::user()->twitter ?? '';
        $this->linkedin = Auth::user()->linkedin ?? '';
    }

    public function updatedAvatar($value): void
    {
        $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);
        
        // There is a bug in livewire where it will not always validate the file type
        // so we have to do it manually and reset the images if it fails, plus create a custom error message
        if (!in_array($extension, ['png', 'jpg', 'jpeg'])) {
            $this->reset('avatar');
        }

        $this->validate(
            [
                'avatar' => ['image', 'mimes:png,jpg,jpeg', 'max:1024'], // 1MB Max
            ],
            [
                'avatar.mimes' => 'Image is not valid. Please upload a valid image.',
            ]
        );
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
            // 'name' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'twitter' => ['nullable', 'string', 'url', 'regex:/^(https?:\/\/)?(www\.)?twitter\.com\/.+/', 'max:255'],
            'linkedin' => ['nullable', 'string', 'url', 'regex:/^(https?:\/\/)?(www\.)?linkedin\.com\/in\/.+/', 'max:255'],
        ]);



        $user->fill(collect($validated)->except('avatar')->toArray());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($this->avatar) {
            $name = 'avatar-' . $user->name . '-' . Ulid::generate(now());
            $fileName = $name . '.' . $this->avatar->extension();

            // Delete old avatar if it exists   
            $user->clearMediaCollection('avatars');

            $user->addMediaFromUrl($this->avatar->temporaryUrl())
                ->usingName($name)
                ->usingFileName($fileName)
                ->toMediaCollection('avatars');
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $path = session('url.intended', RouteServiceProvider::HOME);

            $this->redirect($path);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        @if ($avatar)
            <div class="gap-3 grid grid-cols-2 md:grid-cols-3">
                <img class="aspect-square h-24 object-cover shadow-sm w-24 sm:rounded-md" src="{{ $avatar->temporaryUrl() }}">
            </div>
        @elseif (Auth::user()->getFirstMedia('avatars'))
            <div class="gap-3 grid grid-cols-2 md:grid-cols-3">
                <img class="aspect-square h-24 object-cover shadow-sm w-24 sm:rounded-md" src="{{ Auth::user()->getFirstMedia('avatars')->getAvailableUrl(['thumb']) }}">
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
                <x-input-label for="avatar" :value="__('Avatar')" />
                <x-text-input wire:model="avatar" id="avatar" name="avatar" type="file" accept="image/jpg,image/jpeg,image/png" class="mt-1 p-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <!-- Progress Bar -->
            <div x-show="uploading">
                <x-progress></x-progress>
            </div>
        </div>

        <div>
            <div class="flex justify-between">
                <x-input-label for="name" :value="__('Username')" />
                <span class="text-sm text-gray-600 dark:text-gray-400">Can't be changed</span>
            </div>
            <x-text-input wire:model="name" id="name" name="name" type="text" class="cursor-not-allowed mt-1 block w-full" readonly disabled autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="twitter" :value="__('Twitter')" />
            <x-text-input wire:model="twitter" id="twitter" name="twitter" type="url" class="mt-1 block w-full"  />
            <x-input-error class="mt-2" :messages="$errors->get('twitter')" />
        </div>

        <div>
            <x-input-label for="linkedin" :value="__('LinkedIn')" />
            <x-text-input wire:model="linkedin" id="linkedin" name="linkedin" type="url" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('linkedin')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
