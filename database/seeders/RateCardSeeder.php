<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds services, rate rows and monthly packages exactly as listed in the
 * Sparklenz Official Rate Card. Amounts are whole LKR. Re-running the
 * seeder replaces the rate-card records with the original values.
 */
class RateCardSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ($this->services() as $order => $data) {
                $rates = $data['rates'];
                unset($data['rates']);

                $service = Service::updateOrCreate(['slug' => $data['slug']], $data + ['sort_order' => $order + 1]);
                $service->rates()->delete();

                foreach ($rates as $index => $rate) {
                    $service->rates()->create($rate + ['sort_order' => $index + 1]);
                }
            }

            foreach ($this->packages() as $order => $data) {
                $tiers = $data['tiers'];
                unset($data['tiers']);

                $package = Package::updateOrCreate(['slug' => $data['slug']], $data + ['sort_order' => $order + 1]);
                $package->tiers()->delete();

                foreach ($tiers as $index => $tier) {
                    $package->tiers()->create($tier + ['sort_order' => $index + 1]);
                }
            }
        });
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function services(): array
    {
        return [
            [
                'slug' => 'video-editing',
                'name' => 'Video editing',
                'summary' => 'Short-form reels, YouTube vlog edits and motion graphics.',
                'intro' => 'Short-form content editing for TikTok, Instagram Reels, Facebook Reels, YouTube Shorts, WhatsApp Status and similar platforms.',
                'price_prefix' => 'From',
                'starting_price' => 3800,
                'starting_unit' => 'per 30-second edit',
                'includes' => [
                    ['heading' => 'Basic edit includes', 'items' => ['Raw cut', 'Colour correction', 'Transitions', 'Text / captions']],
                    ['heading' => 'Premium edit includes', 'items' => ['Raw cut', 'Colour correction', 'Modern transitions', 'Animated text / captions', 'Copyright-free music', 'Modern sound design']],
                ],
                'notes' => ['Raw footage must be provided by the client.'],
                'rates' => [
                    ['group_label' => 'Short-form content editing', 'label' => '30 seconds — Basic', 'price' => 3800],
                    ['group_label' => 'Short-form content editing', 'label' => '30 seconds — Premium', 'price' => 4300],
                    ['group_label' => 'Short-form content editing', 'label' => '1 minute — Basic', 'price' => 4800],
                    ['group_label' => 'Short-form content editing', 'label' => '1 minute — Premium', 'price' => 5300],
                    ['group_label' => 'Short-form content editing', 'label' => 'Motion graphic video (1 minute)', 'price' => 5300],
                    ['group_label' => 'Vlog editing (YouTube)', 'label' => 'YouTube vlog editing', 'detail' => 'Customised pricing based on video duration and editing requirements', 'price' => null],
                    ['group_label' => 'Vlog editing (YouTube)', 'label' => 'Every additional 30 seconds', 'price' => 1000],
                    ['group_label' => 'Vlog editing (YouTube)', 'label' => 'Content script prepared by Sparklenz', 'price' => 1200, 'unit' => 'per script'],
                ],
            ],
            [
                'slug' => 'video-production',
                'name' => 'Video production',
                'summary' => 'Full production — shooting, presenting or voice-over, and the final edit.',
                'intro' => 'Full video production with a presenter or a voice-over, from shooting to the finished edit.',
                'price_prefix' => 'From',
                'starting_price' => 8000,
                'starting_unit' => 'per 1-minute video',
                'includes' => [
                    ['heading' => 'Presenting + editing', 'items' => ['Presenter', 'Shooting', 'Full editing']],
                    ['heading' => 'Voice-over + editing', 'items' => ['Voice-over', 'Recording', 'Shooting']],
                ],
                'notes' => [
                    'Transportation charges are not included — on-location shooting will be charged separately.',
                    'Drone shooting is charged separately.',
                    'Rates vary based on presenter / voice-over requirements.',
                ],
                'rates' => [
                    ['group_label' => 'Price per 1-minute video', 'label' => 'Presenting + editing', 'detail' => 'Presenter + shooting + full editing', 'price' => 12000],
                    ['group_label' => 'Price per 1-minute video', 'label' => 'Voice-over + editing', 'detail' => 'Voice-over + recording + shooting', 'price' => 8000],
                    ['group_label' => 'Extras', 'label' => 'Every additional 30 seconds', 'price' => 1500],
                    ['group_label' => 'Extras', 'label' => 'Content script prepared by Sparklenz', 'price' => 1200, 'unit' => 'per script'],
                ],
            ],
            [
                'slug' => 'drone-coverage',
                'name' => 'Drone coverage',
                'summary' => 'A fresh aerial perspective on places, spaces and events.',
                'intro' => 'Drone coverage with raw footage included. Choose flight time by battery, or full coverage for events of up to six hours.',
                'price_prefix' => 'From',
                'starting_price' => 16000,
                'starting_unit' => 'raw footage included',
                'includes' => [
                    ['heading' => 'Every coverage option', 'items' => ['Raw footage included']],
                    ['heading' => 'Full event coverage', 'items' => ['Up to 6 hours', 'Multiple flight sessions']],
                ],
                'notes' => [
                    'Rates may vary depending on project requirements and complexity.',
                    'Transportation charges are not included.',
                    'Location permits and flight permissions are not included.',
                    'Weather conditions and restricted zones may affect operations.',
                ],
                'rates' => [
                    ['group_label' => 'Drone coverage only — raw footage included', 'label' => '1 battery package', 'detail' => 'Up to 20–25 mins', 'price' => 16000],
                    ['group_label' => 'Drone coverage only — raw footage included', 'label' => '2 battery package', 'detail' => 'Up to 45–50 mins', 'price' => 21000],
                    ['group_label' => 'Drone coverage only — raw footage included', 'label' => '3 battery package', 'detail' => 'Up to 70–75 mins', 'price' => 26000],
                    ['group_label' => 'Event coverage — up to 6 hours', 'label' => 'Full event coverage (up to 6 hrs)', 'detail' => 'Multiple flight sessions, raw footage included', 'price' => 30000],
                    ['group_label' => 'Add-ons', 'label' => 'Basic highlight edit', 'price_prefix' => 'From', 'price' => 5000],
                    ['group_label' => 'Add-ons', 'label' => 'Cinematic reel edit', 'price_prefix' => 'From', 'price' => 8000],
                    ['group_label' => 'Add-ons', 'label' => 'Social media vertical reel', 'price_prefix' => 'From', 'price' => 6000],
                ],
            ],
            [
                'slug' => 'graphic-design',
                'name' => 'Graphic design',
                'summary' => 'Social media creatives that make your message stand out.',
                'intro' => 'Social media post design. The price per post depends on how many posts you order together.',
                'price_prefix' => 'From',
                'starting_price' => 800,
                'starting_unit' => 'per post, 5 or more posts',
                'includes' => [],
                'notes' => [],
                'rates' => [
                    ['group_label' => 'Social media post design', 'label' => '1 post', 'price' => 1600],
                    ['group_label' => 'Social media post design', 'label' => '2 posts', 'price' => 1400, 'unit' => 'each'],
                    ['group_label' => 'Social media post design', 'label' => '3 posts', 'price' => 1200, 'unit' => 'each'],
                    ['group_label' => 'Social media post design', 'label' => '4 posts', 'price' => 1000, 'unit' => 'each'],
                    ['group_label' => 'Social media post design', 'label' => '5 or more posts', 'price' => 800, 'unit' => 'each'],
                ],
            ],
            [
                'slug' => 'social-media-handling',
                'name' => 'Social media handling',
                'summary' => 'Captions, scheduling, uploading and community management.',
                'intro' => '“We handle your social media from posting to audience engagement.” Social media handling is part of the Spark Social and Spark Class monthly packages.',
                'price_prefix' => null,
                'starting_price' => null,
                'starting_label' => 'In Spark Social & Spark Class',
                'includes' => [
                    ['heading' => 'Social media handling includes', 'items' => ['Caption writing', 'Content scheduling & uploading', 'Community management']],
                ],
                'notes' => ['Spark Produce does not include social media handling — it can be added separately.'],
                'rates' => [],
            ],
            [
                'slug' => 'additional-services',
                'name' => 'Additional services',
                'summary' => 'Influencer and actor support, and packages shaped around you.',
                'intro' => 'Need something that is not listed? Talk to us about support and packages for your project.',
                'price_prefix' => null,
                'starting_price' => null,
                'starting_label' => 'On request',
                'includes' => [],
                'notes' => [],
                'rates' => [
                    ['group_label' => 'Available on request', 'label' => 'Influencer & actor support', 'detail' => 'Additional charges apply', 'price' => null],
                    ['group_label' => 'Available on request', 'label' => 'Customised packages', 'detail' => 'Based on your requirements', 'price' => null],
                ],
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function packages(): array
    {
        $videoTiers = fn (int $four, int $six, int $eight): array => [
            ['label' => '4 videos / month', 'price' => $four],
            ['label' => '6 videos / month', 'price' => $six],
            ['label' => '8 videos / month', 'price' => $eight],
        ];

        return [
            [
                'slug' => 'spark-edit',
                'name' => 'Spark Edit',
                'tagline' => 'Edit. Refine. Elevate.',
                'type_label' => 'Video editing only',
                'description' => 'Monthly editing for 1-minute videos. You bring the footage — we bring the finish.',
                'additional_item_price' => 5200,
                'additional_item_unit' => 'per additional video',
                'includes' => ['Video editing only (1-minute videos)'],
                'notes' => [],
                'tiers' => $videoTiers(19000, 27500, 35000),
            ],
            [
                'slug' => 'spark-social',
                'name' => 'Spark Social',
                'tagline' => 'Create. Connect. Grow.',
                'type_label' => 'Editing + social media',
                'description' => 'Video editing plus social media handling, to keep your presence consistent every month.',
                'additional_item_price' => 5500,
                'additional_item_unit' => 'per additional video',
                'includes' => ['Video editing', 'Caption writing', 'Content scheduling & uploading', 'Community management'],
                'notes' => [],
                'is_featured' => true,
                'tiers' => $videoTiers(25000, 37500, 47000),
            ],
            [
                'slug' => 'spark-produce',
                'name' => 'Spark Produce',
                'tagline' => 'From concept to content.',
                'type_label' => 'Shooting + editing',
                'description' => 'Full monthly video production — shooting and editing, with or without presenting.',
                'tier_column_label' => 'Videos / month',
                'price_column_label' => 'With presenting',
                'alt_price_column_label' => 'Without presenting',
                'includes' => ['Full video production (shooting + editing)'],
                'notes' => [
                    'Social media handling is not included — can be added separately.',
                    'Content script prepared by Sparklenz: LKR 1,200 per script.',
                    'Transportation charges are not included — charged separately.',
                    'Rates vary based on presenter / voice-over requirements.',
                ],
                'tiers' => [
                    ['label' => '4 videos', 'price' => 45000, 'previous_price' => 48000, 'alt_price' => 35000],
                    ['label' => '6 videos', 'price' => 65000, 'previous_price' => 72000, 'alt_price' => 50000],
                    ['label' => '8 videos', 'price' => 80000, 'previous_price' => 96000, 'alt_price' => 60000],
                ],
            ],
            [
                'slug' => 'spark-class',
                'name' => 'Spark Class',
                'tagline' => 'Smart content for smart classrooms.',
                'type_label' => 'Education content',
                'description' => 'Posts, edited lessons and social handling for educators. The client provides raw footage and content.',
                'tier_column_label' => 'Monthly plan',
                'includes' => ['Clear & professional edits', 'Text / captions', 'Music & SFX', 'Colour correction'],
                'notes' => ['Client provides raw footage / content.'],
                'tiers' => [
                    ['label' => 'Package 01', 'inclusions' => '2 social media posts · 4 × 1-minute videos (editing only) · social media handling', 'price' => 25000],
                    ['label' => 'Package 02', 'inclusions' => '4 social media posts · 6 × 1-minute videos (editing only) · social media handling', 'price' => 40000],
                    ['label' => 'Package 03', 'inclusions' => '6 social media posts · 8 × 1-minute videos (editing only) · social media handling', 'price' => 50000],
                ],
            ],
        ];
    }
}
