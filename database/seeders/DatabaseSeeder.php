<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Prompt;
use App\Models\Platform;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Midjourney         <- stays the same
        // DALL·E             <- replaces ChatGPT
        // Leonardo           <- uses same form as SD
        // Firefly            <- stays the same
        // Stable Diffusion   <- stays the same

        $platforms = [
            'Midjourney',
            'DALL·E',
            'Leonardo',
            'Firefly',
            'Stable Diffusion',
        ];

        foreach ($platforms as $platform) {
            Platform::factory()
                ->create([
                    'name' => $platform,
                    'slug' => Str::slug($platform)
                ]);
        }

        $user = User::factory()
            ->has(Prompt::factory()->count(3))
            ->create([
                'name'     => 'rhyand',
                'email'    => 'andrew.rhyand@gmail.com',
                'password' => '12345678',
                'twitter'  => 'https://twitter.com/rhyand',
                'linkedin' => 'https://linkedin.com/in/rhyand',
            ]);

        $user->prompts->each(function ($prompt) {
            $this->handlePrompt($prompt);
        });

        $user = User::factory()
            ->has(Prompt::factory()->count(7))
            ->create([
                'name'     => 'manny',
                'email'    => 'manny@mannydasilva.com',
                'password' => '12345678',
                'twitter'  => 'https://twitter.com/manny',
                'linkedin' => 'https://linkedin.com/in/manny',
            ]);

        $user->prompts->each(function ($prompt) {
            $this->handlePrompt($prompt);
        });

        // // User::factory(5)
        // //     ->has(Prompt::factory()->count(7))
        // //     ->create();

        // // Prompt::all()->each(function ($prompt) {
        // //     $this->handlePrompt($prompt);
        // // });
    }

    public function handlePrompt(Prompt $prompt): void
    {
        foreach (range(1, random_int(1, 4)) as $i) {
            $ratio = random_int(0, 2) ? '600/600' : (random_int(0, 1) ? '960/540' : '540/960');
            $prompt->addMediaFromUrl('https://picsum.photos/' . $ratio)->toMediaCollection('images');
        }   

        // Attach a random platform to the prompt
        $platform = Platform::inRandomOrder()->first();
        $prompt->platforms()->attach($platform);

        if ($platform->slug !== 'midjourney') {
            // Delete the prompt "tuner" field
            $prompt->tuner = null;
            $prompt->save();
        }

        // Added a negative prompt if the platform is "stable-diffusion"
        if ($platform->slug !== 'stable-diffusion') {
            $prompt->negative_content = null;
            $prompt->save();
        }

        // $tags = ['tag1', 'tag2', 'tag3'];
        // $prompt->attachTags(random_int(0, 3) ? $tags : []);
    }
}
