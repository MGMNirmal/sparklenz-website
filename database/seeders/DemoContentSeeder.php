<?php

namespace Database\Seeders;

use App\Enums\MediaType;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\TeamMember;
use App\Services\MediaStorage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Six clearly labelled concept projects and placeholder team profiles for
 * local review. Every record is flagged is_demo and is hidden publicly
 * unless SHOW_DEMO_CONTENT is enabled (default: off in production).
 *
 * Photography: Unsplash — Manuel Campana, Nathan Dumlao, Master Filmmaker
 * and AYKUT AKTAŞ. Teasers are slow-pan clips generated from those stills.
 */
class DemoContentSeeder extends Seeder
{
    public function run(MediaStorage $media): void
    {
        $assets = database_path('seeders/demo');

        DB::transaction(function () use ($media, $assets): void {
            foreach ($this->projects() as $order => $data) {
                $existing = Project::where('slug', $data['slug'])->first();

                if ($existing) {
                    continue;
                }

                $image = $data['image'] ?? null;
                $teaser = $data['teaser'] ?? null;
                unset($data['image'], $data['teaser']);

                if ($image) {
                    $stored = $media->storeImage($assets.'/'.$image, 'projects');
                    $data += ['cover_path' => $stored['path'], 'cover_width' => $stored['width'], 'cover_height' => $stored['height']];
                }

                if ($teaser) {
                    $data['teaser_path'] = $media->storeTeaserFromPath($assets.'/'.$teaser);
                }

                Project::create($data + [
                    'is_demo' => true,
                    'status' => ProjectStatus::Published,
                    'published_at' => now(),
                    'sort_order' => $order + 1,
                ]);
            }

            if (TeamMember::where('is_demo', true)->doesntExist()) {
                foreach ([['Ayan N.', 'Creative direction'], ['Maya R.', 'Video & post-production'], ['Kiran D.', 'Design & social content']] as $order => [$name, $role]) {
                    TeamMember::create([
                        'name' => $name,
                        'role' => $role,
                        'bio' => 'Placeholder profile. Replace with an approved Sparklenz team member.',
                        'is_demo' => true,
                        'is_published' => true,
                        'sort_order' => $order + 1,
                    ]);
                }
            }
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function projects(): array
    {
        return [
            [
                'title' => 'A slower kind of escape',
                'slug' => 'ceylon-coast',
                'client_label' => 'Ceylon Coast',
                'category' => 'Drone coverage · Travel concept',
                'summary' => 'An aerial travel concept: long, unhurried shots of the southern coastline cut to the rhythm of the tide.',
                'description' => 'Concept project for review. A fictional travel brand used to show how drone coverage and a cinematic reel edit could work together.',
                'media_type' => MediaType::Video,
                'image' => 'coast.jpg',
                'teaser' => 'coast-teaser.mp4',
                'cover_alt' => 'Aerial view of a palm-fringed coastline with turquoise water',
                'is_featured' => true,
            ],
            [
                'title' => 'Everyday, reimagined',
                'slug' => 'still-studio',
                'client_label' => 'Still / Studio',
                'category' => 'Short-form content · Fashion concept',
                'summary' => 'Vertical fashion edits built for Reels and TikTok, with soft colour and a quiet, confident pace.',
                'description' => 'Concept project for review. A fictional label used to show portrait-format short-form editing.',
                'media_type' => MediaType::Video,
                'video_orientation' => 'portrait',
                'image' => 'fashion.jpg',
                'teaser' => 'fashion-teaser.mp4',
                'cover_alt' => 'Fashion portrait of a woman in neutral tones sitting on a metal fence',
                'is_featured' => true,
            ],
            [
                'title' => 'A little ritual. A big feeling.',
                'slug' => 'daily-ritual',
                'client_label' => 'Daily Ritual',
                'category' => 'Social content · Café concept',
                'summary' => 'Warm product stills and social creatives for a neighbourhood café’s morning menu.',
                'description' => 'Concept project for review. A fictional café used to show a stills-led social campaign.',
                'media_type' => MediaType::Image,
                'image' => 'coffee.jpg',
                'cover_alt' => 'Three artisan coffees on a café table surrounded by green plants',
                'is_featured' => true,
            ],
            [
                'title' => 'Made behind the lens',
                'slug' => 'in-the-making',
                'client_label' => 'In the Making',
                'category' => 'Video production · Studio concept',
                'summary' => 'A behind-the-scenes production piece: presenter, shoot and edit, told in one minute.',
                'description' => 'Concept project for review, showing the presenting + editing production format.',
                'media_type' => MediaType::Video,
                'image' => 'studio.jpg',
                'teaser' => 'studio-teaser.mp4',
                'cover_alt' => 'A filmmaker framing a shot behind a professional video camera',
                'is_featured' => true,
            ],
            [
                'title' => 'A new energy for your feed',
                'slug' => 'form-social',
                'client_label' => 'Form / Social',
                'category' => 'Graphic design · Social concept',
                'summary' => 'A post series with one bold idea per frame: less noise, more personality.',
                'description' => 'Concept project for review. Typographic artwork created for this website preview.',
                'media_type' => MediaType::Design,
                'cover_alt' => 'Typographic social post concept reading “Less noise. More you.”',
            ],
            [
                'title' => 'Small lessons. Bigger ideas.',
                'slug' => 'bright-class',
                'client_label' => 'Bright / Class',
                'category' => 'Education content · Learning concept',
                'summary' => 'Bite-sized lesson edits and supporting posts for an online tutor.',
                'description' => 'Concept project for review, illustrating a Spark Class style monthly content plan.',
                'media_type' => MediaType::Design,
                'cover_alt' => 'Education content concept reading “Ideas that go further.”',
            ],
        ];
    }
}
