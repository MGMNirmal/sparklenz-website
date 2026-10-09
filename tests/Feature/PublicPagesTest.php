<?php

namespace Tests\Feature;

use App\Models\Project;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteContentSeeder::class, RateCardSeeder::class]);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function pages(): array
    {
        return [
            'home' => ['/', 'Sparklenz — Beyond the Frame'],
            'about' => ['/about', 'About &amp; Team — Sparklenz'],
            'services' => ['/services', 'Services &amp; Packages — Sparklenz'],
            'work' => ['/work', 'Our Work — Sparklenz'],
            'contact' => ['/contact', 'Contact — Sparklenz'],
        ];
    }

    #[DataProvider('pages')]
    public function test_public_routes_render_with_title_and_canonical(string $path, string $title): void
    {
        $this->get($path)
            ->assertOk()
            ->assertSee('<title>'.$title.'</title>', false)
            ->assertSee('<link rel="canonical" href="'.url($path === '/' ? '' : $path).'">', false)
            ->assertSee('images/brand/logo-dark.png')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_services_page_shows_rate_card_prices(): void
    {
        $response = $this->get('/services')->assertOk();

        foreach (['3,800', '4,300', '12,000', '16,000', '30,000', '1,600', '800', '19,000', '27,500', '35,000', '25,000', '37,500', '47,000', '45,000', '65,000', '80,000', '50,000', '60,000', '40,000', '5,200', '5,500'] as $amount) {
            $response->assertSee($amount);
        }

        // Spark Produce: current with-presenting prices, previous prices struck through.
        $response->assertSeeInOrder(['Previously LKR', '48,000', ', now LKR', '45,000', '35,000'], false);
        $response->assertSee('<s>', false);
    }

    public function test_website_proposal_prices_are_not_shown_as_packages(): void
    {
        $this->get('/services')
            ->assertDontSee('95,000')
            ->assertDontSee('130,000')
            ->assertDontSee('Essential website');
    }

    public function test_only_published_projects_are_listed(): void
    {
        Project::factory()->create(['title' => 'Published film']);
        Project::factory()->draft()->create(['title' => 'Secret draft']);

        $this->get('/work')
            ->assertSee('Published film')
            ->assertDontSee('Secret draft');
    }

    public function test_demo_projects_are_hidden_when_demo_content_is_disabled(): void
    {
        config(['sparklenz.show_demo_content' => false]);
        Project::factory()->demo()->create(['title' => 'Concept escape']);
        Project::factory()->create(['title' => 'Real client film']);

        $this->get('/work')->assertDontSee('Concept escape')->assertSee('Real client film');
        $this->get('/')->assertDontSee('Concept escape');
    }

    public function test_demo_projects_are_labelled_when_shown(): void
    {
        config(['sparklenz.show_demo_content' => true]);
        Project::factory()->demo()->create(['title' => 'Concept escape']);

        $this->get('/work')->assertSee('Concept escape')->assertSee('Concept project');
    }

    public function test_project_payload_is_json_escaped(): void
    {
        Project::factory()->create(['title' => '</script><script>alert(1)</script>']);

        $this->get('/work')
            ->assertOk()
            ->assertDontSee('</script><script>alert(1)', false)
            ->assertSee('\u003C/script\u003E\u003Cscript\u003Ealert(1)', false);
    }

    public function test_work_page_without_projects_shows_empty_state(): void
    {
        $this->get('/work')->assertOk()->assertSee('New work is being prepared');
    }

    public function test_sitemap_lists_public_routes_only(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(url('/services'))
            ->assertDontSee('/admin');
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_about_banner_prefers_landscape_approved_work(): void
    {
        Project::factory()->create(['title' => 'Portrait short', 'cover_path' => 'projects/portrait.webp', 'cover_width' => 720, 'cover_height' => 1280, 'cover_alt' => 'Portrait cover', 'sort_order' => 1]);
        Project::factory()->demo()->create(['title' => 'Demo wide', 'cover_path' => 'projects/demo.webp', 'cover_width' => 1600, 'cover_height' => 900, 'cover_alt' => 'Demo wide cover', 'sort_order' => 2]);
        Project::factory()->create(['title' => 'Client wide', 'cover_path' => 'projects/client.webp', 'cover_width' => 1600, 'cover_height' => 900, 'cover_alt' => 'Client wide cover', 'sort_order' => 3]);

        $this->get('/about')->assertSee('Client wide cover')->assertDontSee('Portrait cover')->assertDontSee('Demo wide cover');
    }
}
