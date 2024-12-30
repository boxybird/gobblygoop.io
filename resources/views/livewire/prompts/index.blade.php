<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public User $user;
}; ?>

<x-slot name="title">Prompt by {{ $user->name }}</x-slot>

<div class="py-12 px-4 sm:gap-12 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-100 dark:bg-gray-900 overflow-hidden sm:rounded-lg">
            <x-user-details-box :user="$user" class="translate-y-4" />
            <livewire:prompts.prompt-grid :user="$user" />
        </div>
    </div>
</div>