<?php

namespace Database\Factories;

use App\Enums\MediaType;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'client_label' => fake()->company(),
            'category' => 'Video production · Test',
            'summary' => fake()->sentence(12),
            'media_type' => MediaType::Video,
            'video_orientation' => 'landscape',
            'youtube_id' => 'M7lc1UVf-VE',
            'is_featured' => false,
            'is_demo' => false,
            'status' => ProjectStatus::Published,
            'published_at' => now(),
            'sort_order' => fake()->numberBetween(1, 50),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Draft, 'published_at' => null]);
    }

    public function demo(): static
    {
        return $this->state(fn () => ['is_demo' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
