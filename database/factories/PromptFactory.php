<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tuner'            => 'raw-' . Str::random(16),
            'content'          => $this->faker->paragraphs(random_int(1, 3), true),
            'negative_content' => $this->faker->paragraphs(random_int(1, 3), true),
        ];
    }
}
