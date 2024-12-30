<?php

use App\Models\User;
use App\Models\Prompt;

test('homepage can be reached', function () {
    $response = $this->get('/');

    $response->assertSeeText(config('app.name'));
    $response->assertStatus(200);
});

test('individual prompt can be reached', function () {
    $user = User::factory()
        ->has(Prompt::factory()->count(1))
        ->create();

    $this->actingAs($user);

    $response = $this->get("{$user->name}/prompts/{$user->prompts->first()->ulid}");

    $response->assertSeeText($user->prompts->first()->content);
    $response->assertStatus(200);
});

test('archive page for user can be reached', function () {
    $user = User::factory()
        ->has(Prompt::factory()->count(1))
        ->create();

    $this->actingAs($user);

    $response = $this->get("{$user->name}/prompts");

    $response->assertSeeText($user->prompts->first()->content);
    $response->assertStatus(200);
});

test('users likes page can be reached', function () {
    $user = User::factory()
        ->has(Prompt::factory()->count(1))
        ->create();

    $prompt = $user->prompts->first();

    $this->actingAs($user);

    $user->like($prompt);

    $response = $this->get("{$user->name}/prompts/likes");

    $response->assertSeeText($prompt->content);
    $response->assertStatus(200);
});

test('privacy page can be reached', function () {
    $response = $this->get('/privacy-policy');

    $response->assertSeeText('Privacy Policy');
    $response->assertStatus(200);
});

test('terms page can be reached', function () {
    $response = $this->get('/terms-of-service');

    $response->assertSeeText('Terms of Service');
    $response->assertStatus(200);
});
