<?php

namespace Tests\Feature;

use App\Enums\EnquiryStatus;
use App\Models\Enquiry;
use App\Models\Package;
use App\Models\PageCopy;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed([SiteContentSeeder::class, RateCardSeeder::class]);
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function projectData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Hill Country Hotel',
            'slug' => '',
            'category' => 'Drone coverage · Hotel',
            'summary' => 'Aerial coverage of a tea-country hotel.',
            'media_type' => 'video',
            'video_orientation' => 'landscape',
            'youtube_url' => 'https://youtu.be/M7lc1UVf-VE',
            'status' => 'published',
            'is_featured' => '1',
            'is_demo' => '0',
        ], $overrides);
    }

    public function test_admin_can_create_a_project_with_cover_teaser_and_gallery(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/projects', $this->projectData([
            'cover' => UploadedFile::fake()->image('cover.jpg', 1600, 1000),
            'cover_alt' => 'Hotel pool from above',
            'teaser' => UploadedFile::fake()->create('teaser.mp4', 900, 'video/mp4'),
            'gallery' => [UploadedFile::fake()->image('one.png', 1200, 800)],
            'gallery_alt' => ['Hotel lobby'],
        ]));

        $project = Project::where('slug', 'hill-country-hotel')->firstOrFail();
        $response->assertRedirect(route('admin.projects.edit', $project));

        $this->assertSame('M7lc1UVf-VE', $project->youtube_id);
        $this->assertStringEndsWith('.webp', $project->cover_path);
        $this->assertSame(1600, $project->cover_width);
        Storage::disk('public')->assertExists($project->cover_path);
        Storage::disk('public')->assertExists(str_replace('.webp', '-sm.webp', $project->cover_path));
        Storage::disk('public')->assertExists($project->teaser_path);
        $this->assertCount(1, $project->media);
        $this->assertNotNull($project->published_at);

        $this->get('/work')->assertSee('Hill Country Hotel')->assertSee('data-youtube="M7lc1UVf-VE"', false);
    }

    public function test_shorts_links_are_accepted_and_other_hosts_rejected(): void
    {
        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData(['youtube_url' => 'https://www.youtube.com/shorts/aqz-KE-bpKQ']));
        $this->assertSame('aqz-KE-bpKQ', Project::first()->youtube_id);

        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData(['title' => 'Another', 'youtube_url' => 'https://vimeo.com/123456']))
            ->assertSessionHasErrors('youtube_url');
        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData(['title' => 'Embed', 'youtube_url' => '<iframe src="https://www.youtube.com/embed/M7lc1UVf-VE"></iframe>']))
            ->assertSessionHasErrors('youtube_url');
    }

    public function test_non_image_uploads_are_rejected(): void
    {
        $fakeImage = UploadedFile::fake()->createWithContent('cover.jpg', '<?php echo "hacked"; ?>');

        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData([
            'cover' => $fakeImage,
            'cover_alt' => 'x',
        ]))->assertSessionHasErrors('cover');

        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData([
            'teaser' => UploadedFile::fake()->createWithContent('teaser.mp4', '<?php echo 1; ?>'),
        ]))->assertSessionHasErrors('teaser');

        $this->assertDatabaseCount('projects', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_small_covers_and_missing_alt_text_are_rejected(): void
    {
        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData([
            'cover' => UploadedFile::fake()->image('tiny.jpg', 300, 200),
        ]))->assertSessionHasErrors(['cover', 'cover_alt']);
    }

    public function test_projects_can_be_reordered_featured_and_unpublished(): void
    {
        $first = Project::factory()->create(['sort_order' => 1]);
        $second = Project::factory()->create(['sort_order' => 2]);

        $this->actingAs($this->admin)->post(route('admin.projects.move', [$second, 'up']))->assertRedirect();
        $this->assertSame(1, $second->fresh()->sort_order);
        $this->assertSame(2, $first->fresh()->sort_order);

        $this->patch(route('admin.projects.toggle', [$first, 'featured']));
        $this->assertTrue($first->fresh()->is_featured);

        $this->patch(route('admin.projects.toggle', [$first, 'published']));
        $this->assertSame('draft', $first->fresh()->status->value);
        $this->get('/work')->assertDontSee($first->title);
    }

    public function test_deleting_a_project_removes_its_files(): void
    {
        $this->actingAs($this->admin)->post('/admin/projects', $this->projectData([
            'cover' => UploadedFile::fake()->image('cover.jpg', 1600, 1000),
            'cover_alt' => 'Pool',
        ]));
        $project = Project::firstOrFail();

        $this->delete(route('admin.projects.destroy', $project))->assertRedirect(route('admin.projects.index'));

        $this->assertModelMissing($project);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_admin_can_update_service_rates(): void
    {
        $service = Service::where('slug', 'graphic-design')->firstOrFail();
        $rates = $service->rates->map(fn ($rate) => $rate->only(['id', 'group_label', 'label', 'detail', 'price_prefix', 'price', 'previous_price', 'unit']))->all();
        $rates[0]['price'] = 1700;
        $rates[1]['delete'] = '1';
        $rates[] = ['id' => null, 'group_label' => 'Social media post design', 'label' => '10 posts', 'price' => 750, 'unit' => 'each'];

        $this->actingAs($this->admin)->put(route('admin.services.update', $service), [
            'name' => 'Graphic design',
            'summary' => 'Social creatives.',
            'price_prefix' => 'From',
            'starting_price' => 750,
            'includes_text' => "What you get:\nPost design\nTwo revisions",
            'notes_text' => 'Prices per post.',
            'is_published' => '1',
            'rates' => $rates,
        ])->assertRedirect();

        $service->refresh();
        $this->assertSame(1700, $service->rates->first()->price);
        $this->assertSame(5, $service->rates->count());
        $this->assertEquals([['heading' => 'What you get', 'items' => ['Post design', 'Two revisions']]], $service->includes);
        $this->get('/services')->assertSee('1,700')->assertSee('10 posts');
    }

    public function test_admin_can_update_package_tiers(): void
    {
        $package = Package::where('slug', 'spark-edit')->firstOrFail();
        $tiers = $package->tiers->map(fn ($tier) => $tier->only(['id', 'label', 'inclusions', 'price', 'previous_price', 'alt_price']))->all();
        $tiers[0]['previous_price'] = 21000;

        $this->actingAs($this->admin)->put(route('admin.packages.update', $package), [
            'name' => 'Spark Edit',
            'type_label' => 'Video editing only',
            'billing_unit' => 'month',
            'tier_column_label' => 'Monthly plan',
            'price_column_label' => 'LKR',
            'tiers' => $tiers,
            'is_published' => '1',
        ])->assertRedirect();

        $this->assertSame(21000, $package->tiers()->first()->previous_price);
        $this->get('/services')->assertSee('21,000');
    }

    public function test_settings_and_copy_are_validated_and_cached_values_refresh(): void
    {
        $this->actingAs($this->admin)->put(route('admin.settings.update'), ['settings' => ['facebook_url' => 'javascript:alert(1)']])
            ->assertSessionHasErrors('settings.facebook_url');

        $this->put(route('admin.settings.update'), ['settings' => ['facebook_url' => 'https://www.facebook.com/sparklenz', 'email' => 'hello@sparklenz.example']])
            ->assertRedirect();
        $this->assertSame('https://www.facebook.com/sparklenz', Setting::map()['facebook_url']);

        $this->put(route('admin.copy.update', 'home'), ['copy' => ['hero_title' => 'Frames with feeling.']])->assertRedirect();
        $this->assertSame('Frames with feeling.', PageCopy::map()['home.hero_title']);

        $this->get('/')->assertSee('Frames with feeling.')->assertSee('https://www.facebook.com/sparklenz');
        $this->get('/contact')->assertSee('hello@sparklenz.example');
    }

    public function test_copy_is_escaped_on_the_public_site(): void
    {
        PageCopy::where('page', 'home')->where('key', 'hero_title')->update(['content' => '<img src=x onerror=alert(1)>']);
        PageCopy::where('page', 'home')->where('key', 'hero_title')->first()->touch();

        $this->get('/')->assertDontSee('<img src=x onerror', false)->assertSee('&lt;img src=x', false);
    }

    public function test_opening_an_enquiry_marks_it_read_and_status_can_change(): void
    {
        $enquiry = Enquiry::factory()->create();

        $this->actingAs($this->admin)->get(route('admin.enquiries.show', $enquiry))->assertOk()->assertSee($enquiry->email);
        $this->assertSame(EnquiryStatus::Read, $enquiry->fresh()->status);

        $this->patch(route('admin.enquiries.update', $enquiry), ['status' => 'replied'])->assertRedirect();
        $this->assertSame(EnquiryStatus::Replied, $enquiry->fresh()->status);
        $this->assertNotNull($enquiry->fresh()->replied_at);

        $this->patch(route('admin.enquiries.update', $enquiry), ['status' => 'archived'])->assertSessionHasErrors('status');
    }
}
