<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')
    ->name('welcome');

Volt::route('/prompts/create', 'prompts.create-new')
    ->name('prompts.create');

Volt::route('/{user}/prompts', 'prompts.index')
    ->name('prompts.index');

Volt::route('/{user}/prompts/likes', 'prompts.likes.index')
    ->name('prompts.likes.index');

Route::redirect('/{user:name}/prompt/{prompt:ulid}', '/{user:name}/prompts/{prompt:ulid}', 301);
Volt::route('/{user:name}/prompts/{prompt:ulid}', 'prompts.show')
    ->name('prompts.show');

Route::redirect('/dashboard', '/profile', 301);
Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('/profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('/about-us', 'pages.about-us')
    ->name('pages.about-us');

Route::view('/privacy-policy', 'pages.privacy-policy')
    ->name('pages.privacy-policy');

Route::view('/terms-of-service', 'pages.terms-of-service')
    ->name('pages.terms-of-service');

require __DIR__ . '/auth.php';
